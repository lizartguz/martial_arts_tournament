<?php

namespace App\Services;

use Carbon\Carbon;

class LogReaderService
{
    public const DEFAULT_MAX_LINES = 5000;

    protected string $logPath;

    /**
     * Define la ruta del archivo de log que sera gestionado.
     */
    public function __construct(?string $logPath = null)
    {
        $this->logPath = $logPath ?: storage_path('logs/laravel.log');
    }

    /**
     * Obtiene informacion basica del archivo de log.
     */
    public function metadata(): array
    {
        if (! file_exists($this->logPath)) {
            return [
                'exists' => false,
                'path' => $this->logPath,
                'size' => 0,
                'formatted_size' => '0 B',
                'last_modified' => null,
            ];
        }

        $size = filesize($this->logPath) ?: 0;

        return [
            'exists' => true,
            'path' => $this->logPath,
            'size' => $size,
            'formatted_size' => $this->formatBytes($size),
            'last_modified' => Carbon::createFromTimestamp(filemtime($this->logPath))->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Lee y agrupa las entradas recientes del log de Laravel.
     */
    public function entries(int $maxLines = self::DEFAULT_MAX_LINES): array
    {
        if (! file_exists($this->logPath) || ! is_readable($this->logPath)) {
            return [];
        }

        $lines = $this->tailLines($maxLines);
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(?<datetime>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(?<env>[^.]+)\.(?<level>[A-Z]+):\s+(?<message>.*)$/', $line, $matches)) {
                if ($current !== null) {
                    $entries[] = $this->finalizeEntry($current);
                }

                $parsedMessage = $this->parseMessage($matches['message']);

                $current = [
                    'datetime' => $matches['datetime'],
                    'env' => $matches['env'],
                    'level' => strtolower($matches['level']),
                    'message' => $parsedMessage['message'],
                    'context' => $parsedMessage['context'],
                    'context_json' => $parsedMessage['context_json'],
                    'trace' => [],
                    'raw' => [$line],
                ];

                continue;
            }

            if ($current !== null) {
                $current['trace'][] = $line;
                $current['raw'][] = $line;
            }
        }

        if ($current !== null) {
            $entries[] = $this->finalizeEntry($current);
        }

        return array_reverse($entries);
    }

    /**
     * Filtra entradas por texto, nivel y rango de fechas.
     */
    public function filterEntries(array $entries, array $filters): array
    {
        $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));
        $level = trim((string) ($filters['level'] ?? ''));
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        return array_values(array_filter($entries, function (array $entry) use ($search, $level, $fromDate, $toDate) {
            if ($level !== '' && $entry['level'] !== $level) {
                return false;
            }

            if ($fromDate && $entry['datetime'] < $fromDate . ' 00:00:00') {
                return false;
            }

            if ($toDate && $entry['datetime'] > $toDate . ' 23:59:59') {
                return false;
            }

            if ($search !== '') {
                $haystack = mb_strtolower($entry['message'] . ' ' . $entry['raw'] . ' ' . $entry['context_json']);

                if (! str_contains($haystack, $search)) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * Limpia completamente el contenido del archivo de log.
     */
    public function clear(): void
    {
        $directory = dirname($this->logPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->logPath, '', LOCK_EX);
    }

    /**
     * Elimina una entrada especifica del log manteniendo las demas.
     */
    public function deleteEntry(string $entryId): bool
    {
        $entries = array_reverse($this->entries(PHP_INT_MAX));
        $filteredEntries = [];
        $wasDeleted = false;

        foreach ($entries as $entry) {
            if ($entry['id'] === $entryId) {
                $wasDeleted = true;
                continue;
            }

            $filteredEntries[] = rtrim($entry['raw']);
        }

        if (! $wasDeleted) {
            return false;
        }

        $content = implode(PHP_EOL, $filteredEntries);

        if ($content !== '') {
            $content .= PHP_EOL;
        }

        file_put_contents($this->logPath, $content, LOCK_EX);

        return true;
    }

    /**
     * Lee las ultimas lineas del archivo sin cargarlo completo en memoria.
     */
    protected function tailLines(int $maxLines): array
    {
        $handle = fopen($this->logPath, 'rb');

        if ($handle === false) {
            return [];
        }

        $buffer = '';
        $chunkSize = 8192;
        $position = -1;
        $lineCount = 0;

        fseek($handle, 0, SEEK_END);
        $fileSize = ftell($handle);

        while ($lineCount <= $maxLines && abs($position) <= $fileSize) {
            $seek = max($fileSize + $position - $chunkSize + 1, 0);
            $readSize = min($chunkSize, $fileSize - $seek);

            fseek($handle, $seek);
            $chunk = fread($handle, $readSize);

            if ($chunk === false) {
                break;
            }

            $buffer = $chunk . $buffer;
            $lineCount = substr_count($buffer, "\n");
            $position -= $chunkSize;
        }

        fclose($handle);

        $lines = preg_split('/\r\n|\n|\r/', trim($buffer));

        if ($lines === false) {
            return [];
        }

        return array_slice($lines, -$maxLines);
    }

    /**
     * Separa el mensaje principal del contexto JSON cuando existe.
     */
    protected function parseMessage(string $message): array
    {
        $message = trim($message);
        $context = null;
        $contextJson = '';

        $jsonStart = strrpos($message, '{');

        if ($jsonStart !== false) {
            $candidate = trim(substr($message, $jsonStart));
            $decoded = json_decode($candidate, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $context = $decoded;
                $contextJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $message = trim(substr($message, 0, $jsonStart));
            }
        }

        return [
            'message' => $message,
            'context' => $context,
            'context_json' => $contextJson ?: '',
        ];
    }

    /**
     * Normaliza una entrada agrupada y genera su identificador estable.
     */
    protected function finalizeEntry(array $entry): array
    {
        $entry['raw'] = implode(PHP_EOL, $entry['raw']);
        $entry['trace'] = trim(implode(PHP_EOL, $entry['trace']));
        $entry['id'] = sha1($entry['datetime'] . '|' . $entry['level'] . '|' . $entry['raw']);

        return $entry;
    }

    /**
     * Convierte bytes a una unidad legible para la interfaz.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}
