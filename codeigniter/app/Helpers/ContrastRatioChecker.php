<?php
namespace App\Helpers;

class ContrastRatioChecker {
    public static function isOkContrast(string $hex1, string $hex2) {
        if (!$hex1 && !$hex2) return true;
        if (!$hex1 || !$hex2) return false;

        $rgb1 = ContrastRatioChecker::getRGB($hex1);
        $lum1 = ContrastRatioChecker::getLuminance($rgb1);

        $rgb2 = ContrastRatioChecker::getRGB($hex2);
        $lum2 = ContrastRatioChecker::getLuminance($rgb2);


        $ratio = $lum1 > $lum2 
        ? (($lum2 + 0.05) / ($lum1 + 0.05))
        : (($lum1 + 0.05) / ($lum2 + 0.05));

        return $ratio < 1/4.5;
    }

    public static function getDarkerColor(string $hex1, string $hex2) {
        if (!$hex1 && !$hex2) return null;
        if (!$hex1 || !$hex2) return null;

        $rgb1 = ContrastRatioChecker::getRGB($hex1);
        $lum1 = ContrastRatioChecker::getLuminance($rgb1);

        $rgb2 = ContrastRatioChecker::getRGB($hex2);
        $lum2 = ContrastRatioChecker::getLuminance($rgb2);

        return $lum1 < $lum2 ? $hex1 : $hex2;
    }

    private static function getLuminance(array $rgb) {
        $results = [0, 0, 0];

        for ($i = 0; $i < count($rgb); ++$i) {
            $v = $rgb[$i] / 255;
            $results[$i] = $v <= 0.03928 ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        }

        return $results[0] * 0.2126 + $results[1] * 0.7152 + $results[2] * 0.0722;
    }

    private static function getRGB(string $hex) {
        return [
            intval(substr($hex, 0, 2), 16),
            intval(substr($hex, 3, 2), 16),
            intval(substr($hex, 5, 2), 16)
        ];

    }
}