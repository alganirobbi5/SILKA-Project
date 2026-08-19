<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class PdfRenderer
{
    public static function render(string $html, string $filename): Response
    {
        if (self::memoryLimitBytes() < 1024 * 1024 * 1024) {
            ini_set('memory_limit', '1024M');
        }

        $fontDir = storage_path('fonts/dompdf');
        $tempDir = storage_path('app/dompdf');

        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0775, true);
        }
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('fontDir', $fontDir);
        $options->set('fontCache', $fontDir);
        $options->set('tempDir', $tempDir);

        $dompdf = new Dompdf($options);

        static::registerFonts($dompdf);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private static function registerFonts(Dompdf $dompdf): void
    {
        $dir = storage_path('fonts/segoe-ui');

        $fonts = [
            'SegoeUI.ttf' => 'normal',
            'SegoeUI-Bold.ttf' => 'bold',
            'SegoeUI-Semibold.ttf' => '600',
            'SegoeUI-Light.ttf' => '100',
        ];

        $metrics = $dompdf->getFontMetrics();

        foreach ($fonts as $file => $weight) {
            $path = $dir . '/' . $file;
            if (!is_file($path)) {
                continue;
            }

            $metrics->registerFont([
                'family' => 'Segoe UI',
                'style' => 'normal',
                'weight' => $weight,
            ], $path);
        }
    }

    private static function memoryLimitBytes(): int
    {
        $limit = ini_get('memory_limit');
        if ($limit === false || $limit === '-1') {
            return PHP_INT_MAX;
        }

        $value = (int) $limit;
        $unit = strtolower(substr($limit, -1));
        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
        }

        return $value;
    }
}