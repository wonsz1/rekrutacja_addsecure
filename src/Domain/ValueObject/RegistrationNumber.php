<?php

namespace Domain\ValueObject;

final class RegistrationNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $normalizedValue = strtoupper(trim($value));
        $this->validate($normalizedValue);
        $this->value = $normalizedValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Registration number cannot be empty');
        }

        if (strlen($value) < 4) {
            throw new \InvalidArgumentException('Registration number must be at least 4 characters long');
        }

        if (strlen($value) > 16) {
            throw new \InvalidArgumentException('Registration number cannot exceed 16 characters');
        }

        if (!preg_match('/^[A-Z0-9]+$/', $value)) {
            throw new \InvalidArgumentException(
                'Registration number can only contain uppercase letters and numbers'
            );
        }
    }

    public function isValidFormat(): bool
    {
        // Example of Polish registration number validation
        // Format: XX-XXXXX or XXX-XXXX (where X is a letter or number)
        $polishPattern = '/^[A-Z]{2,3}-[A-Z0-9]{4,5}$/';
        return preg_match($polishPattern, $this->value) === 1;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
