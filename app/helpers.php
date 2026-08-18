<?php

if (!function_exists('rupiah')) {
    /**
     * Format angka menjadi Rupiah tanpa mengubah nilai database.
     */
    function rupiah($value, bool $withSymbol = true)
    {
        $formatted = number_format((float) $value, 2, ',', '.');

        return $withSymbol ? 'Rp' . $formatted : $formatted;
    }
}
