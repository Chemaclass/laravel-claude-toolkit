<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf('Invalid email address: %s', $value)
            );
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
