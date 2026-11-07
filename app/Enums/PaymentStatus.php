<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Scheduled = 'Scheduled';
    case Due = 'Due';
    case Matured = 'Matured';
    case PartiallyPaid = 'Partially Paid';
    case Settled = 'Settled';
    case Overdue = 'Overdue';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
