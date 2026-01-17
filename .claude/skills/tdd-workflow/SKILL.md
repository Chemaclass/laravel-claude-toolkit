# TDD Workflow Skill

## Activation Triggers
- Creating or modifying test files
- Running PHPUnit tests
- Discussing testing strategy
- Implementing new features (test first!)

## The TDD Cycle

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│    ┌─────────┐      ┌─────────┐      ┌──────────┐         │
│    │   RED   │ ───► │  GREEN  │ ───► │ REFACTOR │ ──┐     │
│    │  Write  │      │  Write  │      │ Improve  │   │     │
│    │ Failing │      │ Minimal │      │   Code   │   │     │
│    │  Test   │      │  Code   │      │          │   │     │
│    └─────────┘      └─────────┘      └──────────┘   │     │
│         ▲                                           │     │
│         └───────────────────────────────────────────┘     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Test Directory Structure

Tests are organized by module:

```
tests/
├── Unit/
│   └── {Module}/                     # Per-module unit tests
│       ├── Domain/
│       │   ├── Entity/
│       │   └── ValueObject/
│       └── Application/
│           ├── Command/
│           └── Query/
├── Integration/
│   └── {Module}/                     # Per-module integration tests
├── Feature/
│   └── {Module}/                     # Per-module feature tests
└── Support/
    └── Builders/
        └── {Module}/                 # Per-module test data factories
```

## Test Templates

### Unit Test - Entity (`tests/Unit/{Module}/Domain/Entity/{Name}Test.php`)
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Module}\Domain\Entity;

use Modules\{Module}\Domain\Entity\{Name};
use Modules\{Module}\Domain\Entity\{Name}Id;
use PHPUnit\Framework\TestCase;

final class {Name}Test extends TestCase
{
    public function test_can_create_with_valid_data(): void
    {
        $id = {Name}Id::generate();

        $entity = {Name}::create($id);

        $this->assertEquals($id, $entity->id());
    }
}
```

### Unit Test - Value Object (`tests/Unit/{Module}/Domain/ValueObject/{Name}Test.php`)
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Module}\Domain\ValueObject;

use Modules\{Module}\Domain\ValueObject\{Name};
use Modules\{Module}\Domain\Exception\Invalid{Name};
use PHPUnit\Framework\TestCase;

final class {Name}Test extends TestCase
{
    public function test_creates_from_valid_string(): void
    {
        $vo = {Name}::fromString('valid-value');
        $this->assertEquals('valid-value', $vo->toString());
    }

    public function test_throws_exception_for_invalid_value(): void
    {
        $this->expectException(Invalid{Name}::class);
        {Name}::fromString('invalid');
    }

    public function test_equals_same_value(): void
    {
        $vo1 = {Name}::fromString('value');
        $vo2 = {Name}::fromString('value');
        $this->assertTrue($vo1->equals($vo2));
    }
}
```

### Unit Test - Handler (`tests/Unit/{Module}/Application/Command/{Name}HandlerTest.php`)
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Module}\Application\Command;

use Modules\{Module}\Application\Command\{Name};
use Modules\{Module}\Application\Command\{Name}Handler;
use Modules\{Module}\Domain\Repository\{Entity}Repository;
use PHPUnit\Framework\TestCase;

final class {Name}HandlerTest extends TestCase
{
    public function test_handles_command(): void
    {
        $repository = $this->createMock({Entity}Repository::class);
        $repository->expects($this->once())
            ->method('save');

        $handler = new {Name}Handler($repository);

        $handler(new {Name}(id: 'test-id'));
    }
}
```

### Integration Test - Repository (`tests/Integration/{Module}/{Name}RepositoryTest.php`)
```php
<?php

declare(strict_types=1);

namespace Tests\Integration\{Module};

use Modules\{Module}\Domain\Entity\{Name};
use Modules\{Module}\Domain\Entity\{Name}Id;
use Modules\{Module}\Infrastructure\Persistence\Eloquent\Repository\{Name}EloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class {Name}RepositoryTest extends TestCase
{
    use RefreshDatabase;

    private {Name}EloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new {Name}EloquentRepository();
    }

    public function test_saves_and_retrieves_entity(): void
    {
        $entity = {Name}::create({Name}Id::generate());

        $this->repository->save($entity);
        $found = $this->repository->findById($entity->id());

        $this->assertNotNull($found);
        $this->assertTrue($entity->id()->equals($found->id()));
    }
}
```

### Feature Test - HTTP (`tests/Feature/{Module}/{Name}Test.php`)
```php
<?php

declare(strict_types=1);

namespace Tests\Feature\{Module};

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class {Name}Test extends TestCase
{
    use RefreshDatabase;

    public function test_creates_via_api(): void
    {
        $response = $this->postJson('/api/{resource}', [
            'id' => 'test-id',
        ]);

        $response->assertCreated();
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->postJson('/api/{resource}', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }
}
```

## Test Builders

```php
// tests/Support/Builders/{Module}/{Name}Builder.php
final class {Name}Builder
{
    public static function create(?{Name}Id $id = null): {Name}
    {
        return {Name}::create($id ?? {Name}Id::generate());
    }
}
```

## Running Tests

```bash
./vendor/bin/sail test                           # All tests
./vendor/bin/sail test tests/Unit/User           # By module
./vendor/bin/sail test --filter UserTest         # By filter
./vendor/bin/sail test --coverage                # With coverage
```

## Best Practices

| Practice | Description |
|----------|-------------|
| Descriptive names | `test_throws_exception_when_email_invalid` |
| One concept per test | Don't test multiple behaviors |
| AAA pattern | Arrange → Act → Assert |
| Use Builders | Avoid duplicating test data setup |
| Mock at boundaries | Mock interfaces, not concrete classes |
