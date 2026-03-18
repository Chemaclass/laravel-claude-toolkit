# Create Value Object

Create a domain value object following DDD principles with test-first approach.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <ValueObjectName>` (e.g., `User Email`, `Order Money`)

## Instructions

1. **Parse arguments**: Extract module and value object name from `$ARGUMENTS`

2. **Create the test first** (TDD):
   ```
   tests/Unit/<Module>/Domain/ValueObject/<ValueObjectName>Test.php
   ```
   - Test creation with valid data
   - Test creation with invalid data throws exception
   - Test `equals()` method with same and different values
   - Test serialization methods (`toString()`, `toArray()`, etc.)

3. **Run the test** to see it fail (Red phase):
   ```bash
   ./vendor/bin/sail test --filter <ValueObjectName>Test
   ```

4. **Create the value object class**:
   ```
   modules/<Module>/Domain/ValueObject/<ValueObjectName>.php
   ```
   - Pure PHP, no Laravel dependencies
   - Private constructor with static factory method(s)
   - Immutable (readonly properties)
   - Validate invariants in factory method
   - Implement `equals()` for comparison

5. **Run the test again** to see it pass (Green phase)

6. **Refactor if needed** while keeping tests green

## Value Object Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\ValueObject;

use Modules\<Module>\Domain\Exception\Invalid<ValueObjectName>;

final readonly class <ValueObjectName>
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        if (empty($value)) {
            throw Invalid<ValueObjectName>::empty();
        }

        // Add validation logic here

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

## ID Value Object Template

For entity identifiers, use this specialized template:

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\Entity;

use Ramsey\Uuid\Uuid;

final readonly class <EntityName>Id
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID: {$value}");
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

## Exception Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\Exception;

final class Invalid<ValueObjectName> extends \DomainException
{
    public static function empty(): self
    {
        return new self('<ValueObjectName> cannot be empty');
    }

    public static function withFormat(string $value): self
    {
        return new self("Invalid <ValueObjectName> format: {$value}");
    }

    public static function withReason(string $reason): self
    {
        return new self("Invalid <ValueObjectName>: {$reason}");
    }
}
```

## Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\<Module>\Domain\ValueObject;

use Modules\<Module>\Domain\ValueObject\<ValueObjectName>;
use Modules\<Module>\Domain\Exception\Invalid<ValueObjectName>;
use PHPUnit\Framework\TestCase;

final class <ValueObjectName>Test extends TestCase
{
    public function test_can_create_from_valid_string(): void
    {
        $value = <ValueObjectName>::fromString('valid-value');

        $this->assertSame('valid-value', $value->toString());
    }

    public function test_throws_exception_for_invalid_value(): void
    {
        $this->expectException(Invalid<ValueObjectName>::class);

        <ValueObjectName>::fromString('');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $value1 = <ValueObjectName>::fromString('same-value');
        $value2 = <ValueObjectName>::fromString('same-value');

        $this->assertTrue($value1->equals($value2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $value1 = <ValueObjectName>::fromString('value-one');
        $value2 = <ValueObjectName>::fromString('value-two');

        $this->assertFalse($value1->equals($value2));
    }
}
```

## Common Patterns

| Pattern | Factory | Example |
|---------|---------|---------|
| Simple string | `fromString()` | Email, Name |
| UUID identifier | `generate()`, `fromString()` | UserId, OrderId |
| Composite | `create()` with multiple args | Address, Money |
| Numeric | `fromCents()`, `fromFloat()` | Money, Percentage |

## Test Base Class Note

Domain tests use `PHPUnit\Framework\TestCase` directly (no Laravel).

## Checklist
- [ ] Test file created first
- [ ] Test fails initially (Red)
- [ ] Value object class created
- [ ] Exception class created
- [ ] Test passes (Green)
- [ ] Code refactored if needed
- [ ] No Laravel dependencies in Domain layer
- [ ] `equals()` method implemented
- [ ] Factory method validates invariants
