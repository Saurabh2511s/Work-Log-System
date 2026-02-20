<?php

namespace App\Support;

class TimeUtil
{
    public static function minutesToHuman(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return str_pad((string)$h, 2, '0', STR_PAD_LEFT) . 'h ' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . 'm';
    }
}