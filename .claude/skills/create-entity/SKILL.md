# Create Domain Entity

Create a domain entity following DDD principles with test-first approach.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <EntityName>` (e.g., `User User`, `Order OrderItem`)

## Instructions

1. **Parse arguments**: Extract module and entity name from `$ARGUMENTS`

2. **Create the test first** (TDD):
   ```
   tests/Unit/<Module>/Domain/Entity/<EntityName>Test.php
   ```
   - Test entity creation with valid data
   - Test entity creation with invalid data throws exception
   - Test all getters return expected values
   - Test any business rules/invariants

3. **Run the test** to see it fail (Red phase):
   ```bash
   ./vendor/bin/sail test --filter <EntityName>Test
   ```

4. **Create the entity class**:
   ```
   modules/<Module>/Domain/Entity/<EntityName>.php
   ```
   - Pure PHP, no Laravel dependencies
   - Private constructor with named static factory method `create()`
   - Immutable properties (readonly)
   - Value Objects for complex attributes
   - Validate invariants in constructor

5. **Run the test again** to see it pass (Green phase)

6. **Refactor if needed** while keeping tests green

## Entity Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\Entity;

use Modules\<Module>\Domain\Exception\Invalid<EntityName>;

final readonly class <EntityName>
{
    private function __construct(
        private <EntityName>Id $id,
        // Add other properties
    ) {
    }

    public static function create(
        <EntityName>Id $id,
        // Add other parameters
    ): self {
        // Validate invariants here
        return new self($id);
    }

    public function id(): <EntityName>Id
    {
        return $this->id;
    }
}
```

## Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\<Module>\Domain\Entity;

use Modules\<Module>\Domain\Entity\<EntityName>;
use Modules\<Module>\Domain\Entity\<EntityName>Id;
use PHPUnit\Framework\TestCase;

final class <EntityName>Test extends TestCase
{
    public function test_can_create_entity_with_valid_data(): void
    {
        $id = <EntityName>Id::generate();

        $entity = <EntityName>::create($id);

        $this->assertEquals($id, $entity->id());
    }

    public function test_throws_exception_for_invalid_data(): void
    {
        $this->expectException(Invalid<EntityName>::class);

        // Test with invalid data
    }
}
```

## Entity ID Template

Create the ID using `/create-value-object` command's ID template:
```
modules/<Module>/Domain/Entity/<EntityName>Id.php
```

> See `create-value-object.md` for the full ID Value Object template.

## Exception Template

Create the exception in `modules/<Module>/Domain/Exception/Invalid<EntityName>.php`:

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\Exception;

final class Invalid<EntityName> extends \DomainException
{
    public static function missingField(string $field): self
    {
        return new self("<EntityName> requires {$field}");
    }

    public static function invalidState(string $reason): self
    {
        return new self("Invalid <EntityName> state: {$reason}");
    }

    public static function withReason(string $reason): self
    {
        return new self("Invalid <EntityName>: {$reason}");
    }
}
```

## Test Base Class Note

Domain tests should use `PHPUnit\Framework\TestCase` directly (no Laravel), keeping the domain layer framework-agnostic. Only use `Tests\TestCase` for integration or feature tests that require Laravel's application context.

## Checklist
- [ ] Test file created first
- [ ] Test fails initially (Red)
- [ ] Entity ID value object created
- [ ] Entity class created
- [ ] Exception class created
- [ ] Test passes (Green)
- [ ] Code refactored if needed
- [ ] No Laravel dependencies in Domain layer
