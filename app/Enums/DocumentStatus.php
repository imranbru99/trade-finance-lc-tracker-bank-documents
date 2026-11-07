<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Pending = 'Pending';
    case Uploaded = 'Uploaded';
    case Approved = 'Approved';
    case Verified = 'Verified';
    case Discrepant = 'Discrepant';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return match ($this) {
            self::Uploaded => 'Uploaded',
            self::Approved => 'Approved',
            default => $this->value,
        };
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
