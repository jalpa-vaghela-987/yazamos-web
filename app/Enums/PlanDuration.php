<?php

namespace App\Enums;

abstract class PlanDuration
{
    const DAILY = 'daily';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';

    const ALL_DURATIONS = [
        self::DAILY,
        self::MONTHLY,
        self::YEARLY,
    ];

    public static function types()
    {
        return self::ALL_DURATIONS;
    }
}
