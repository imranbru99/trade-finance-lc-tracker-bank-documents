<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Planned = 'Planned';
    case Booked = 'Booked';
    case ReadyForShipment = 'Ready for Shipment';
    case Shipped = 'Shipped';
    case InTransit = 'In Transit';
    case Arrived = 'Arrived';
    case CustomsProcessing = 'Customs Processing';
    case Delivered = 'Delivered';
    case Delayed = 'Delayed';
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
