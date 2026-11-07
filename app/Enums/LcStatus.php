<?php

namespace App\Enums;

enum LcStatus: string
{
    case Draft = 'Draft';
    case PendingInternalReview = 'Pending Internal Review';
    case PendingApproval = 'Pending Approval';
    case SubmittedToBank = 'Submitted to Bank';
    case Issued = 'Issued';
    case Advised = 'Advised';
    case AmendmentRequested = 'Amendment Requested';
    case AmendmentPending = 'Amendment Pending';
    case Active = 'Active';
    case PartiallyShipped = 'Partially Shipped';
    case FullyShipped = 'Fully Shipped';
    case DocumentsSubmitted = 'Documents Submitted';
    case UnderBankExamination = 'Under Bank Examination';
    case DiscrepancyFound = 'Discrepancy Found';
    case Accepted = 'Accepted';
    case PaymentPending = 'Payment Pending';
    case Matured = 'Matured';
    case Settled = 'Settled';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';
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
