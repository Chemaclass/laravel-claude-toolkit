<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObject;

use InvalidArgumentException;

readonly class Uuid
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    protected function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): static
    {
        if (! self::isValid($value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid UUID format: %s', $value)
            );
        }

        return new static(strtolower($value));
    }

    public static function generate(): static
    {
        return new static(self::uuid4());
    }

    public static function isValid(string $value): bool
    {
        return preg_match(self::PATTERN, $value) === 1;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function uuid4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
