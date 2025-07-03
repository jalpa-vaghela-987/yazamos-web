<?php

namespace App\Enums;

abstract class PhaseType
{
    const TIMELINE = 'timeline';
    const BUDGET = 'budget';

    const ALL_TYPES = [
        self::TIMELINE,
        self::BUDGET,
    ];

    public static function types()
    {
        return self::ALL_TYPES;
    }
}
