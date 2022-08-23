<?php
namespace CodeIgniter\Validation;

// custom
class PasswordRules
{
    const MIN_DIGITS = 1;
    const MIN_ALPHABETIC = 1;
    const MIN_SYMBOLS = 1;
    const MIN_LENGTH = 10;

    public function strong_password(?string $str = null, ?string &$error = null): bool
    {
        if (empty($str) || strlen($str) < PasswordRules::MIN_LENGTH) {
            $error = 'Password is too short';
            return false;
        }

        $digits = 0;
        $alpabetic = 0;
        $symbols = 0;
        $length = strlen($str);

        for ($i = 0; $i < $length; ++$i) {
            $c = $str[$i];

            if ($c >= '0' && $c <= '9') {
                $digits++;
            } elseif (($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z')) {
                $alpabetic++;
            } else {
                $symbols++;
            }
        }

        if (($digits >= PasswordRules::MIN_DIGITS) 
            && ($alpabetic >= PasswordRules::MIN_ALPHABETIC) 
            && ($symbols >= PasswordRules::MIN_SYMBOLS)) 
        {
            return true;
        }

        $error = 'Password must contain at least ' . PasswordRules::MIN_DIGITS . ' digit, ' . PasswordRules::MIN_ALPHABETIC . ' alphabetic, ' . PasswordRules::MIN_SYMBOLS . ' symbol.';
        return false;
    }
}