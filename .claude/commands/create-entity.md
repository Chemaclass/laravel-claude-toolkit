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

use Modules\<Module>\Domain\Exception\Invalid<EntityName>Exception;

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
        $this->expectException(Invalid<EntityName>Exception::class);

        // Test with invalid data
    }
}
```

## Checklist
- [ ] Test file created first
- [ ] Test fails initially (Red)
- [ ] Entity class created
- [ ] Test passes (Green)
- [ ] Code refactored if needed
- [ ] No Laravel dependencies in Domain layer
