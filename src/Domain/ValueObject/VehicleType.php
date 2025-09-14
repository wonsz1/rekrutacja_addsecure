<?php

namespace Domain\ValueObject;

enum VehicleType: string
{
    case PASSENGER = 'passenger';
    case BUS = 'bus';
    case TRUCK = 'truck';

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