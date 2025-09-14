<?php

namespace Domain\ValueObject;

enum VehicleType: string
{
    case PASSENGER = 'passenger';
    case BUS = 'bus';
    case TRUCK = 'truck';

    public function getDisplayName(): string
    {
        return match($this) {
            self::PASSENGER => 'Passenger Car',
            self::BUS => 'Bus',
            self::TRUCK => 'Truck',
        };
    }

    public static function getAllTypes(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}