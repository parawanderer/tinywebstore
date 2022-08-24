<?php
namespace App\Helpers;

require_once ROOTPATH . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

// http://htmlpurifier.org/docs
// to enable the "fancy" html editor
class HtmlSanitizer {
    private static $config = null;
    private static $purifier = null;

    public static function getPurifier() {
        if (HtmlSanitizer::$config == null) {
            HtmlSanitizer::$config = \HTMLPurifier_Config::createDefault();
            HtmlSanitizer::$purifier = new \HTMLPurifier(HtmlSanitizer::$config);
        }

        return HtmlSanitizer::$purifier;
    }

    public static function sanitize(string|null $html) {
        if (!$html) return $html;
        
        return HtmlSanitizer::getPurifier()->purify($html);
    }

}