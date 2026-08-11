<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use TCPDF;

class DownloadDataStationPdfService
{
    protected const LEFT_MARGIN = 6;
    protected const TOP_MARGIN = 8;
    protected const RIGHT_MARGIN = 6;
    protected const BOTTOM_MARGIN = 8;
    protected const HEADER_HEIGHT = 9;
    protected const ROW_HEIGHT = 5.2;
    protected const ABBREVIATED_HEADER_THRESHOLD = 12;

    /**
     * Ejecuta la operación generate del servicio.
     */
    public function generate(string $path, array $heads, iterable $rows, array $metadata, string $paperSize): int
    {
        File::ensureDirectoryExists(dirname($path));

        $pdf = $this->createPdf($paperSize, $metadata['title'] ?? 'Reporte');
        $pdf->AddPage();
        $this->writeReportHeader($pdf, $metadata);
        $columnWidths = $this->getColumnWidths($pdf, $heads);
        $this->writeTableHeader($pdf, $heads, $columnWidths);
        $rowCount = 0;

        foreach ($rows as $row) {
            if ($this->shouldAddPage($pdf)) {
                $pdf->AddPage();
                $this->writeReportHeader($pdf, $metadata);
                $this->writeTableHeader($pdf, $heads, $columnWidths);
            }

            $this->writeDataRow($pdf, $heads, $row, $columnWidths, $rowCount % 2 === 0);
            $rowCount++;
        }

        $pdf->Output($path, 'F');

        return $rowCount;
    }

    /**
     * Ejecuta la operación create pdf del servicio.
     */
    protected function createPdf(string $paperSize, string $title): TCPDF
    {
        $pdf = new TCPDF('L', 'mm', strtoupper($paperSize), true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle($title);
        $pdf->SetMargins(self::LEFT_MARGIN, self::TOP_MARGIN, self::RIGHT_MARGIN);
        $pdf->SetAutoPageBreak(false, self::BOTTOM_MARGIN);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCompression(true);
        $pdf->SetFont('helvetica', '', 7);

        return $pdf;
    }

    /**
     * Ejecuta la operación write report header del servicio.
     */
    protected function writeReportHeader(TCPDF $pdf, array $metadata): void
    {
        $pdf->SetTextColor(6, 95, 70);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, $metadata['title'] ?? 'Reporte', 0, 1, 'L');

        $pdf->SetTextColor(55, 65, 81);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(0, 4, $metadata['station'] ?? '', 0, 1, 'L');
        $pdf->Cell(0, 4, $metadata['period'] ?? '', 0, 1, 'L');
        $pdf->Cell(0, 4, $metadata['generatedAt'] ?? '', 0, 1, 'L');
        $pdf->Ln(2);
    }

    /**
     * Devuelve column widths solicitado.
     */
    protected function getColumnWidths(TCPDF $pdf, array $heads): array
    {
        $columnCount = max(count($heads), 1);
        $availableWidth = $pdf->getPageWidth() - self::LEFT_MARGIN - self::RIGHT_MARGIN;
        $defaultWidth = $availableWidth / $columnCount;
        $widths = [];

        foreach ($heads as $key => $head) {
            $widths[$key] = $defaultWidth;
        }

        return $widths;
    }

    /**
     * Ejecuta la operación write table header del servicio.
     */
    protected function writeTableHeader(TCPDF $pdf, array $heads, array $columnWidths): void
    {
        $pdf->SetFillColor(4, 120, 87);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(209, 213, 219);
        $pdf->SetFont('helvetica', 'B', $this->getHeaderFontSize($heads));

        foreach ($heads as $key => $head) {
            $label = $this->getHeaderLabel($key, $head, count($heads));
            $pdf->MultiCell($columnWidths[$key], self::HEADER_HEIGHT, $label, 1, 'C', true, 0);
        }

        $pdf->Ln();
    }

    /**
     * Ejecuta la operación write data row del servicio.
     */
    protected function writeDataRow(TCPDF $pdf, array $heads, array $row, array $columnWidths, bool $fill): void
    {
        $pdf->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 251 : 255);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetDrawColor(229, 231, 235);
        $pdf->SetFont('helvetica', '', 4.8);

        foreach ($heads as $key => $head) {
            $value = $this->formatCellValue($key, (string) ($row[$key] ?? ''));

            if (str_contains($value, "\n")) {
                $pdf->MultiCell($columnWidths[$key], self::ROW_HEIGHT, $value, 1, 'C', $fill, 0);
                continue;
            }

            $pdf->Cell($columnWidths[$key], self::ROW_HEIGHT, $this->shortText($value, $columnWidths[$key]), 1, 0, 'C', $fill);
        }

        $pdf->Ln();
    }

    /**
     * Determina si corresponde add page.
     */
    protected function shouldAddPage(TCPDF $pdf): bool
    {
        return $pdf->GetY() + self::ROW_HEIGHT > $pdf->getPageHeight() - self::BOTTOM_MARGIN;
    }

    /**
     * Formatea cell value para mostrarlo en pantalla.
     */
    protected function formatCellValue(string $key, string $value): string
    {
        if ($key === 'receipt_date' && preg_match('/^(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})$/', $value, $matches)) {
            return $matches[1] . "\n" . $matches[2];
        }

        return $value;
    }

    /**
     * Ejecuta la operación short text del servicio.
     */
    protected function shortText(string $text, float $columnWidth): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
        $maxLength = max(8, (int) floor($columnWidth * 1.8));

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($text) > $maxLength ? mb_substr($text, 0, $maxLength - 3) . '...' : $text;
        }

        return strlen($text) > $maxLength ? substr($text, 0, $maxLength - 1) . '...' : $text;
    }

    /**
     * Devuelve la etiqueta visible de get header.
     */
    protected function getHeaderLabel(string $key, array $head, int $columnCount): string
    {
        $unit = trim($head['unit'] ?? '');

        if ($columnCount >= self::ABBREVIATED_HEADER_THRESHOLD) {
            $label = $key === 'receipt_date' ? 'Fecha' : $key;
            return trim($label . ' ' . $unit);
        }

        return trim(($head['label'] ?? $key) . ' ' . $unit);
    }

    /**
     * Devuelve header font size solicitado.
     */
    protected function getHeaderFontSize(array $heads): float
    {
        return count($heads) >= self::ABBREVIATED_HEADER_THRESHOLD ? 4.5 : 5.4;
    }
}
