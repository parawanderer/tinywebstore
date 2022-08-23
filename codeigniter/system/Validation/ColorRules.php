<?php
namespace CodeIgniter\Validation;

// custom
class ColorRules
{
    private const LENGTH = 7;
    private const HEX_COLOR_REGEX = '/#([a-f0-9]{3}){1,2}\b/i';

    public function hexcolor(?string $str = null, ?string &$error = null): bool
    {   
        if ($str == null) {
            return true;
        }

        if (strlen($str) != ColorRules::LENGTH || $str[0] !== '#') {
            $error = 'Color must be a valid hex color string';
            return false;
        }

        if (preg_match(ColorRules::HEX_COLOR_REGEX, $str)) {
            return true;
        }

        $error = 'Color must be a valid hex color string';
        return false;
    }
}