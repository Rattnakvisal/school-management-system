<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdminUserReportExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    public function __construct(
        private readonly string $titleText,
        private readonly string $subtitle,
        private readonly string $sheetName,
        private readonly string $accentColor,
        private readonly array $headings,
        private readonly array $rows,
        private readonly array $cards = [],
        private readonly array $filters = [],
    ) {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return substr($this->sheetName, 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $columnCount = max(1, count($this->headings));
                $lastColumn = Coordinate::stringFromColumnIndex($columnCount);
                $headingRow = 6;
                $dataStartRow = $headingRow + 1;
                $dataEndRow = $dataStartRow + max(count($this->rows), 1) - 1;
                $tableEndRow = max($headingRow, $dataEndRow);

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->mergeCells("A4:{$lastColumn}4");
                $sheet->mergeCells("A5:{$lastColumn}5");

                $sheet->setCellValue('A1', $this->titleText);
                $sheet->setCellValue('A2', $this->subtitle);
                $sheet->setCellValue('A3', 'Generated on ' . now()->format('F j, Y g:i A'));
                $sheet->setCellValue('A4', $this->buildSummaryLine());
                $sheet->setCellValue('A5', $this->buildFilterLine());

                $sheet->freezePane("A{$dataStartRow}");
                $sheet->setAutoFilter("A{$headingRow}:{$lastColumn}{$headingRow}");

                $sheet->getStyle("A1:{$lastColumn}2")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $this->accentColor],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ]);

                $sheet->getStyle("A1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                ]);

                $sheet->getStyle("A2")->applyFromArray([
                    'font' => [
                        'size' => 11,
                    ],
                ]);

                $sheet->getStyle("A3:A5")->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '334155'],
                    ],
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A{$headingRow}:{$lastColumn}{$headingRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F172A'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A{$dataStartRow}:{$lastColumn}{$tableEndRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                for ($row = $dataStartRow; $row <= $tableEndRow; $row++) {
                    if (($row - $dataStartRow) % 2 === 1) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()->setFillType(Fill::FILL_SOLID);
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()->getStartColor()->setRGB('F8FAFC');
                    }
                }

                $sheet->getStyle("A1:{$lastColumn}{$tableEndRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A1:{$lastColumn}{$tableEndRow}")->getFont()->setName('Arial');

                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(22);
                $sheet->getDefaultRowDimension()->setRowHeight(20);

                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.35);
                $sheet->getPageMargins()->setRight(0.25);
                $sheet->getPageMargins()->setBottom(0.35);
                $sheet->getPageMargins()->setLeft(0.25);
            },
        ];
    }

    private function buildSummaryLine(): string
    {
        if ($this->cards === []) {
            return 'Summary: No summary data available.';
        }

        $parts = array_map(
            static fn(array $card): string => sprintf('%s: %s', $card['label'] ?? 'Metric', $card['value'] ?? '-'),
            $this->cards
        );

        return 'Summary: ' . implode('   |   ', $parts);
    }

    private function buildFilterLine(): string
    {
        if ($this->filters === []) {
            return 'Filters: All records included.';
        }

        $parts = array_map(
            static fn(array $filter): string => sprintf('%s: %s', $filter['label'] ?? 'Filter', $filter['value'] ?? '-'),
            $this->filters
        );

        return 'Filters: ' . implode('   |   ', $parts);
    }
}
