<?php

namespace Tests\Unit;

use App\Services\LogReaderService;
use Tests\TestCase;

class LogReaderServiceTest extends TestCase
{
    protected string $logPath;

    /**
     * Prepara un archivo temporal para probar el lector de logs.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logPath = storage_path('framework/testing/log-reader-test.log');

        if (! is_dir(dirname($this->logPath))) {
            mkdir(dirname($this->logPath), 0755, true);
        }
    }

    /**
     * Elimina el archivo temporal usado por cada prueba.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }

        parent::tearDown();
    }

    /**
     * Verifica que una entrada con contexto JSON sea interpretada correctamente.
     */
    public function testParsesLaravelLogEntriesWithJsonContext(): void
    {
        file_put_contents($this->logPath, '[2026-06-07 15:06:53] local.INFO: [PublicStationFeed] Access {"ip":"127.0.0.1","user_agent":"Test browser"}' . PHP_EOL);

        $entries = (new LogReaderService($this->logPath))->entries();

        $this->assertCount(1, $entries);
        $this->assertSame('2026-06-07 15:06:53', $entries[0]['datetime']);
        $this->assertSame('local', $entries[0]['env']);
        $this->assertSame('info', $entries[0]['level']);
        $this->assertSame('[PublicStationFeed] Access', $entries[0]['message']);
        $this->assertSame('127.0.0.1', $entries[0]['context']['ip']);
    }

    /**
     * Verifica que las trazas multilinea queden asociadas a su entrada.
     */
    public function testGroupsMultilineTracesWithTheirEntry(): void
    {
        file_put_contents($this->logPath, implode(PHP_EOL, [
            '[2026-06-07 16:00:00] local.ERROR: Something failed',
            '#0 C:\app\File.php(10): Example',
            '#1 {main}',
        ]));

        $entries = (new LogReaderService($this->logPath))->entries();

        $this->assertCount(1, $entries);
        $this->assertStringContainsString('#0 C:\app\File.php(10): Example', $entries[0]['trace']);
        $this->assertStringContainsString('#1 {main}', $entries[0]['raw']);
    }

    /**
     * Verifica el filtrado combinado por nivel, fecha y texto.
     */
    public function testFiltersByLevelDateAndSearchText(): void
    {
        file_put_contents($this->logPath, implode(PHP_EOL, [
            '[2026-06-06 10:00:00] local.INFO: First message {"ip":"127.0.0.1"}',
            '[2026-06-07 10:00:00] local.ERROR: Target message {"ip":"10.0.0.1"}',
        ]));

        $reader = new LogReaderService($this->logPath);
        $filtered = $reader->filterEntries($reader->entries(), [
            'search' => 'target',
            'level' => 'error',
            'from_date' => '2026-06-07',
            'to_date' => '2026-06-07',
        ]);

        $this->assertCount(1, $filtered);
        $this->assertSame('Target message', $filtered[0]['message']);
    }

    /**
     * Verifica que se elimine solo el registro seleccionado del log.
     */
    public function testDeleteEntryRemovesSelectedLogRecord(): void
    {
        file_put_contents($this->logPath, implode(PHP_EOL, [
            '[2026-06-06 10:00:00] local.INFO: First message',
            '[2026-06-07 10:00:00] local.ERROR: Target message',
            '#0 C:\app\File.php(10): Example',
            '[2026-06-08 10:00:00] local.WARNING: Last message',
        ]));

        $reader = new LogReaderService($this->logPath);
        $target = collect($reader->entries())->firstWhere('message', 'Target message');

        $this->assertTrue($reader->deleteEntry($target['id']));

        $content = file_get_contents($this->logPath);

        $this->assertStringContainsString('First message', $content);
        $this->assertStringContainsString('Last message', $content);
        $this->assertStringNotContainsString('Target message', $content);
        $this->assertStringNotContainsString('#0 C:\app\File.php(10): Example', $content);
    }
}
