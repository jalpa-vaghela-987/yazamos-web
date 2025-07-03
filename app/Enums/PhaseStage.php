<?php

namespace App\Enums;

use Illuminate\Support\Facades\Log;

abstract class PhaseStage
{
    const PRE = 'pre';
    const CURRENT = 'current';
    const POST = 'post';

    const ALL_STAGES = [
        self::PRE,
        self::CURRENT,
        self::POST,
    ];
    const STAGE_COLORS = [
        self::PRE => '#025CB4',
        self::CURRENT => '#648A10',
        self::POST => '#CF5E23',
    ];


    public static function stages()
    {
        return self::ALL_STAGES;
    }

    public static function getColor($stage)
    {
        log::info('stage',[$stage]);
        return self::STAGE_COLORS[$stage] ?? '#000000';
    }

    public static function getAllStagesWithColors()
    {
        $stages = [];
        foreach (self::ALL_STAGES as $stage) {
            $stages[] = [
                'value' => $stage,
                'text' => ucfirst(str_replace('-', ' ', $stage)),
                'color' => self::STAGE_COLORS[$stage]
            ];
        }
        return $stages;
    }

}
