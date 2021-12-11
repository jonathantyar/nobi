<?php

namespace App\Helpers;

class Calculate
{
    /**
     * Round down no matter high the last value
     */
    public static function roundDown(Float $calc, Int $roundDown)
    {
        return floor($calc * pow(10, $roundDown)) / pow(10, $roundDown);
    }
}
