<?php

namespace App\Exports;

use ZipArchive;

/**
 * Generator file .xlsx minimal tanpa dependency eksternal.
 * Menghasilkan file OOXML Spreadsheet asli (bukan HTML/CSV).
 */
class TransaksiReportExport
{
    /**
     * Buat konten biner .xlsx.
     *
     * @param \Illuminate\Support\Collection $transaksis
     * @param array $totals
     * @return string
     */
    public static function build($transaksis, array $totals)
    {
        $rows = [];
        $rows[] = ['No', 'Tanggal', 'Jenis', 'Kategori', 'Kode COA', 'Nama COA', 'Keterangan', 'Nominal'];

        $no = 1;
        foreach ($transaksis as $t) {
            $rows[] = [
                $no,
                $t->tanggal instanceof \Carbon\CarbonInterface
                    ? $t->tanggal->format('Y-m-d')
                    : date('Y-m-d', strtotime($t->tanggal)),
                self::labelJenis($t->jenis),
                optional($t->kategori)->kategori ?? '',
                optional($t->coa)->kode_coa ?? '',
                optional($t->coa)->nama_coa ?? '',
                (string) $t->keterangan,
                (float) $t->nominal,
            ];
            $no++;
        }

        // Baris total
        $rows[] = [];
        $rows[] = ['Total Pemasukan', '', '', '', '', '', '', (float) $totals['pemasukan']];
        $rows[] = ['Total Pengeluaran', '', '', '', '', '', '', (float) $totals['pengeluaran']];
        $rows[] = ['Selisih Bersih', '', '', '', '', '', '', (float) $totals['selisih']];

        return self::generateXlsx($rows);
    }

    protected static function labelJenis($jenis)
    {
        return $jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
    }

    protected static function sanitizeFormula($value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $first = $value[0];
            if (in_array($first, ['=', '+', '-', '@'])) {
                return "'" . $value;
            }
        }
        return $value;
    }

    protected static function generateXlsx(array $rows)
    {
        $sheetXml = self::buildSheetXml($rows);

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan Transaksi" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.00"/></numFmts>'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';

        $zip = new ZipArchive();
        $tmp = tempnam(sys_get_temp_dir(), 'silka_');
        if ($zip->open($tmp, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Tidak dapat membuat file xlsx.');
        }

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->close();

        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    protected static function buildSheetXml(array $rows)
    {
        $body = '';
        foreach ($rows as $row) {
            $cells = '';
            foreach ($row as $cell) {
                $value = self::sanitizeFormula($cell);
                if (is_float($value)) {
                    $cells .= '<c s="2" t="n"><v>' . $value . '</v></c>';
                } elseif (is_int($value)) {
                    $cells .= '<c t="n"><v>' . $value . '</v></c>';
                } else {
                    $value = (string) $value;
                    $escaped = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $cells .= '<c t="inlineStr"><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
                }
            }
            $body .= '<row>' . $cells . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $body . '</sheetData>'
            . '</worksheet>';
    }
}
