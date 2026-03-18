# Create Use Case (Command/Query Handler)

Create an application use case following CQRS pattern with test-first approach.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <Type> <Name>` (e.g., `User Command CreateUser`, `Order Query GetOrderById`)

## Instructions

1. **Parse arguments**: Extract module, type (Command/Query), and name from `$ARGUMENTS`

2. **Create the test first** (TDD):
   ```
   tests/Unit/<Module>/Application/<Type>/<Name>HandlerTest.php
   ```
   - Test happy path with mocked repository
   - Test edge cases and error conditions
   - Test that correct repository methods are called

3. **Run the test** to see it fail (Red phase):
   ```bash
   ./vendor/bin/sail test --filter <Name>HandlerTest
   ```

4. **Create the DTO class**:
   ```
   modules/<Module>/Application/<Type>/<Name>.php
   ```
   - Immutable data transfer object
   - Only primitive types or Value Objects
   - No behavior, just data

5. **Create the Handler class**:
   ```
   modules/<Module>/Application/<Type>/<Name>Handler.php
   ```
   - Single `__invoke()` method
   - Inject repository interfaces (not implementations)
   - Orchestrate domain objects
   - Return appropriate response (entity, DTO, or void for commands)

6. **Run the test again** to see it pass (Green phase)

7. **Refactor if needed** while keeping tests green

## Command DTO Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Application\Command;

final readonly class <Name>
{
    public function __construct(
        public string $id,
        // Add other properties
    ) {
    }
}
```

## Command Handler Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Application\Command;

use Modules\<Module>\Domain\Repository\<Entity>Repository;

final readonly class <Name>Handler
{
    public function __construct(
        private <Entity>Repository $repository,
    ) {
    }

    public function __invoke(<Name> $command): void
    {
        // 1. Reconstitute or create domain entity
        // 2. Execute domain logic
        // 3. Persist changes
    }
}
```

## Query DTO Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Application\Query;

final readonly class <Name>
{
    public function __construct(
        public string $id,
    ) {
    }
}
```

## Query Handler Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Application\Query;

use Modules\<Module>\Domain\Repository\<Entity>Repository;

final readonly class <Name>Handler
{
    public function __construct(
        private <Entity>Repository $repository,
    ) {
    }

    public function __invoke(<Name> $query): ?<Entity>
    {
        return $this->repository->findById(
            <Entity>Id::fromString($query->id)
        );
    }
}
```

## Handler Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\<Module>\Application\<Type>;

use Modules\<Module>\Application\<Type>\<Name>;
use Modules\<Module>\Application\<Type>\<Name>Handler;
use Modules\<Module>\Domain\Repository\<Entity>Repository;
use PHPUnit\Framework\TestCase;

final class <Name>HandlerTest extends TestCase
{
    public function test_handles_<name>_successfully(): void
    {
        $repository = $this->createMock(<Entity>Repository::class);
        $repository->expects($this->once())
            ->method('save');

        $handler = new <Name>Handler($repository);

        $handler(new <Name>(
            id: 'test-id',
        ));
    }
}
```

## Error Handling Pattern

```php
public function __invoke(Update<Entity> $command): void
{
    $entity = $this->repository->findById(<Entity>Id::fromString($command->id));
    if ($entity === null) {
        throw <Entity>NotFound::withId($command->id);
    }
    $this->repository->save($entity->updateWith($command->data));
}
```

## Handler Test with Exception

```php
public function test_throws_exception_when_entity_not_found(): void
{
    $repository = $this->createMock(<Entity>Repository::class);
    $repository->method('findById')->willReturn(null);
    $handler = new Update<Entity>Handler($repository);

    $this->expectException(<Entity>NotFound::class);
    $handler(new Update<Entity>(id: 'non-existent-id'));
}
```

## Test Base Class Note

Application tests use `PHPUnit\Framework\TestCase` with mocked repositories for fast, isolated tests.

## Checklist
- [ ] Test file created first
- [ ] Test fails initially (Red)
- [ ] DTO class created
- [ ] Handler class created
- [ ] Exception handling implemented
- [ ] Test passes (Green)
- [ ] Code refactored if needed
- [ ] Handler only depends on interfaces
