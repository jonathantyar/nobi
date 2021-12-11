<?php

namespace App\Helpers;

class Calculate
{
    public static function roundDown(Int $calc, Int $roundDown)
    {
        return round($calc, $roundDown, PHP_ROUND_HALF_DOWN);
    }
}
