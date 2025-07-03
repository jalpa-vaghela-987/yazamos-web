<?php

namespace App\Enums;

abstract class TransactionStatus
{
    const PENDING = 'pending';
    const CANCEL = 'cancel';
    const COMPLETE = 'complete';
    const FAILED = 'failed';

    const ALL_STATUSES = [
        self::PENDING,
        self::CANCEL,
        self::COMPLETE,
        self::FAILED
    ];

    const INITIAL_STATUS = self::PENDING;
}
