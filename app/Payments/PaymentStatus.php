<?php

namespace App\Payments;

class PaymentStatus
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const SUCCEEDED = 'succeeded';
    public const FAILED = 'failed';
    public const REFUNDED = 'refunded';
    public const CANCELLED = 'cancelled';

    public static function isValid(string $status): bool
    {
        return in_array($status, [
            self::PENDING,
            self::PROCESSING,
            self::SUCCEEDED,
            self::FAILED,
            self::REFUNDED,
            self::CANCELLED
        ]);
    }

    public static function isFinal(string $status): bool
    {
        return in_array($status, [
            self::SUCCEEDED,
            self::FAILED,
            self::REFUNDED,
            self::CANCELLED
        ]);
    }
}