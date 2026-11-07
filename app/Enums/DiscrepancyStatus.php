<?php

namespace App\Enums;

enum DiscrepancyStatus: string
{
    case Identified = 'Identified';
    case UnderReview = 'Under Review';
    case AwaitingCorrection = 'Awaiting Correction';
    case SubmittedForWaiver = 'Submitted for Waiver';
    case WaiverPending = 'Waiver Pending';
    case Resolved = 'Resolved';
    case Rejected = 'Rejected';
    case Closed = 'Closed';

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
