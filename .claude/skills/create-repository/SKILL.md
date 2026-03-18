# Create Repository

Create a repository interface in Domain layer with Eloquent and InMemory implementations.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <EntityName>` (e.g., `User User`, `Order Order`)

## Instructions

1. **Parse arguments**: Extract module and entity name from `$ARGUMENTS`

2. **Create the repository interface** (Domain layer):
   `modules/<Module>/Domain/Repository/<EntityName>Repository.php`

3. **Create the InMemory implementation** (for tests):
   `modules/<Module>/Infrastructure/Persistence/InMemory/<EntityName>InMemoryRepository.php`

4. **Create integration test**:
   `tests/Integration/<Module>/<EntityName>EloquentRepositoryTest.php`

5. **Run test** to see it fail (Red phase)

6. **Create the Eloquent implementation**:
   `modules/<Module>/Infrastructure/Persistence/Eloquent/Repository/<EntityName>EloquentRepository.php`

7. **Create/update Eloquent Model** if needed

8. **Run test** to see it pass (Green phase)

9. **Register binding** in Module ServiceProvider

## Templates

### Interface (Domain)
```php
namespace Modules\<Module>\Domain\Repository;

interface <EntityName>Repository
{
    public function save(<EntityName> $entity): void;
    public function findById(<EntityName>Id $id): ?<EntityName>;
    public function delete(<EntityName>Id $id): void;
}
```

### InMemory Implementation
```php
namespace Modules\<Module>\Infrastructure\Persistence\InMemory;

final class <EntityName>InMemoryRepository implements <EntityName>Repository
{
    /** @var array<string, <EntityName>> */
    private array $entities = [];

    public function save(<EntityName> $entity): void {
        $this->entities[$entity->id()->toString()] = $entity;
    }

    public function findById(<EntityName>Id $id): ?<EntityName> {
        return $this->entities[$id->toString()] ?? null;
    }

    public function delete(<EntityName>Id $id): void {
        unset($this->entities[$id->toString()]);
    }
}
```

### Eloquent Implementation
```php
namespace Modules\<Module>\Infrastructure\Persistence\Eloquent\Repository;

final readonly class <EntityName>EloquentRepository implements <EntityName>Repository
{
    public function save(<EntityName> $entity): void {
        <EntityName>Model::updateOrCreate(['id' => $entity->id()->toString()], $this->toArray($entity));
    }

    public function findById(<EntityName>Id $id): ?<EntityName> {
        $model = <EntityName>Model::find($id->toString());
        return $model ? $this->toDomain($model) : null;
    }

    public function delete(<EntityName>Id $id): void {
        <EntityName>Model::destroy($id->toString());
    }

    private function toArray(<EntityName> $entity): array { /* map entity to array */ }
    private function toDomain(<EntityName>Model $model): <EntityName> { /* map model to entity */ }
}
```

### Integration Test
```php
namespace Tests\Integration\<Module>;

final class <EntityName>EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_and_retrieve_entity(): void {
        $repository = new <EntityName>EloquentRepository();
        $entity = <EntityName>::create(<EntityName>Id::generate());

        $repository->save($entity);
        $found = $repository->findById($entity->id());

        $this->assertNotNull($found);
        $this->assertEquals($entity->id(), $found->id());
    }

    public function test_returns_null_when_not_found(): void {
        $repository = new <EntityName>EloquentRepository();
        $found = $repository->findById(<EntityName>Id::fromString('non-existent'));
        $this->assertNull($found);
    }
}
```

## Test Base Class Note

Repository integration tests should use `Tests\TestCase` with the `RefreshDatabase` trait since they interact with the real database. This ensures proper test isolation and database state reset between tests.

## Checklist
- [ ] Interface created in Domain layer
- [ ] InMemory implementation created
- [ ] Integration test created and fails
- [ ] Eloquent implementation created
- [ ] Eloquent Model created/updated
- [ ] Integration test passes
- [ ] Binding registered in Module ServiceProvider
