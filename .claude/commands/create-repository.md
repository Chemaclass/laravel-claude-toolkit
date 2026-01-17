# Create Repository

Create a repository interface in Domain layer with Eloquent and InMemory implementations.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <EntityName>` (e.g., `User User`, `Order Order`)

## Instructions

1. **Parse arguments**: Extract module and entity name from `$ARGUMENTS`

2. **Create the repository interface** (Domain layer):
   ```
   modules/<Module>/Domain/Repository/<EntityName>Repository.php
   ```
   - Pure PHP interface
   - Methods: `save()`, `findById()`, `delete()`, etc.
   - Use domain types (Entity, ValueObject) not primitives

3. **Create the InMemory implementation** (for tests):
   ```
   modules/<Module>/Infrastructure/Persistence/InMemory/<EntityName>InMemoryRepository.php
   ```
   - Array-based storage
   - Used in unit tests for fast execution

4. **Create integration test for Eloquent repository**:
   ```
   tests/Integration/<Module>/<EntityName>EloquentRepositoryTest.php
   ```
   - Test with real database
   - Use RefreshDatabase trait

5. **Run integration test** to see it fail (Red phase):
   ```bash
   ./vendor/bin/sail test --filter <EntityName>EloquentRepositoryTest
   ```

6. **Create the Eloquent implementation**:
   ```
   modules/<Module>/Infrastructure/Persistence/Eloquent/Repository/<EntityName>EloquentRepository.php
   ```
   - Depends on Eloquent Model
   - Maps between Entity and Model

7. **Create/update Eloquent Model if needed**:
   ```
   modules/<Module>/Infrastructure/Persistence/Eloquent/Model/<EntityName>Model.php
   ```

8. **Run integration test** to see it pass (Green phase)

9. **Register binding in Module ServiceProvider**:
   ```php
   // modules/<Module>/Infrastructure/Provider/<Module>ServiceProvider.php
   $this->app->bind(
       <EntityName>Repository::class,
       <EntityName>EloquentRepository::class
   );
   ```

## Interface Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Domain\Repository;

use Modules\<Module>\Domain\Entity\<EntityName>;
use Modules\<Module>\Domain\Entity\<EntityName>Id;

interface <EntityName>Repository
{
    public function save(<EntityName> $entity): void;

    public function findById(<EntityName>Id $id): ?<EntityName>;

    public function delete(<EntityName>Id $id): void;
}
```

## InMemory Implementation Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Infrastructure\Persistence\InMemory;

use Modules\<Module>\Domain\Entity\<EntityName>;
use Modules\<Module>\Domain\Entity\<EntityName>Id;
use Modules\<Module>\Domain\Repository\<EntityName>Repository;

final class <EntityName>InMemoryRepository implements <EntityName>Repository
{
    /** @var array<string, <EntityName>> */
    private array $entities = [];

    public function save(<EntityName> $entity): void
    {
        $this->entities[$entity->id()->toString()] = $entity;
    }

    public function findById(<EntityName>Id $id): ?<EntityName>
    {
        return $this->entities[$id->toString()] ?? null;
    }

    public function delete(<EntityName>Id $id): void
    {
        unset($this->entities[$id->toString()]);
    }
}
```

## Eloquent Implementation Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Infrastructure\Persistence\Eloquent\Repository;

use Modules\<Module>\Domain\Entity\<EntityName>;
use Modules\<Module>\Domain\Entity\<EntityName>Id;
use Modules\<Module>\Domain\Repository\<EntityName>Repository;
use Modules\<Module>\Infrastructure\Persistence\Eloquent\Model\<EntityName>Model;

final readonly class <EntityName>EloquentRepository implements <EntityName>Repository
{
    public function save(<EntityName> $entity): void
    {
        <EntityName>Model::updateOrCreate(
            ['id' => $entity->id()->toString()],
            $this->toArray($entity)
        );
    }

    public function findById(<EntityName>Id $id): ?<EntityName>
    {
        $model = <EntityName>Model::find($id->toString());

        return $model ? $this->toDomain($model) : null;
    }

    public function delete(<EntityName>Id $id): void
    {
        <EntityName>Model::destroy($id->toString());
    }

    private function toArray(<EntityName> $entity): array
    {
        return [
            'id' => $entity->id()->toString(),
            // Map other properties
        ];
    }

    private function toDomain(<EntityName>Model $model): <EntityName>
    {
        return <EntityName>::create(
            <EntityName>Id::fromString($model->id),
            // Map other properties
        );
    }
}
```

## Integration Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Integration\<Module>;

use Modules\<Module>\Domain\Entity\<EntityName>;
use Modules\<Module>\Domain\Entity\<EntityName>Id;
use Modules\<Module>\Infrastructure\Persistence\Eloquent\Repository\<EntityName>EloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class <EntityName>EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private <EntityName>EloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new <EntityName>EloquentRepository();
    }

    public function test_can_save_and_retrieve_entity(): void
    {
        $entity = <EntityName>::create(
            <EntityName>Id::generate(),
        );

        $this->repository->save($entity);

        $found = $this->repository->findById($entity->id());

        $this->assertNotNull($found);
        $this->assertEquals($entity->id(), $found->id());
    }

    public function test_returns_null_when_not_found(): void
    {
        $found = $this->repository->findById(
            <EntityName>Id::fromString('non-existent')
        );

        $this->assertNull($found);
    }
}
```

## Checklist
- [ ] Interface created in Domain layer
- [ ] InMemory implementation created
- [ ] Integration test created and fails
- [ ] Eloquent implementation created
- [ ] Eloquent Model created/updated
- [ ] Integration test passes
- [ ] Binding registered in Module ServiceProvider
