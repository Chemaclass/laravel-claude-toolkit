---
globs: tests/**/*.php
---

# Testing Conventions

## Coverage Standards

- **Minimum 80% coverage** for all new code
- Domain layer should have **95%+ coverage**
- Critical business logic requires **100% coverage**

## TDD Workflow

Always follow red-green-refactor:

1. **RED**: Write failing test first
2. **GREEN**: Write minimum code to pass
3. **REFACTOR**: Improve while keeping tests green
4. **VERIFY**: Check coverage with `./vendor/bin/phpunit --coverage-text`

## Test Commands

```bash
sail composer test              # Full suite: analyze + parallel tests
sail composer test:fast         # Skip analysis, just run parallel tests
sail composer test:unit         # Unit tests only
sail composer test:integration  # Integration tests only
sail composer test:feature      # Feature tests only
```

## Test Naming Conventions

| Test Type | File Pattern |
|-----------|--------------|
| Entity Test | `tests/Unit/{Module}/Domain/Entity/{Name}Test.php` |
| Value Object Test | `tests/Unit/{Module}/Domain/ValueObject/{Name}Test.php` |
| Handler Test | `tests/Unit/{Module}/Application/{Type}/{Name}HandlerTest.php` |
| Repository Test | `tests/Integration/{Module}/{Name}RepositoryTest.php` |
| Feature Test | `tests/Feature/{Module}/{Name}Test.php` |
| Model Factory | `modules/{Module}/Infrastructure/Persistence/Eloquent/Model/{Name}ModelFactory.php` |

## Test Method Naming

```php
// Method: test_<what>_<condition>_<expected>
public function test_create_user_with_valid_email_returns_user(): void
public function test_create_user_with_invalid_email_throws_exception(): void

// Or use descriptive it_* naming
public function it_creates_a_user_with_valid_data(): void
public function it_throws_when_email_is_invalid(): void
```

## Rules

- Use `#[Test]` attribute (not `/** @test */` docblock)
- Use `mock()` directly instead of `Mockery::mock()`
- Use InMemory repository implementations for unit tests
- Use `NullTransactionManager` for handlers that need `TransactionManager`
- Invoke handlers with `($this->handler)($command)` syntax

## Unit Test Pattern (Handler)

```php
final class CreateOrderHandlerTest extends TestCase {
    private OrderInMemoryRepository $repository;
    /** @var DomainEvent[] */
    private array $dispatchedEvents = [];
    private CreateOrderHandler $handler;

    protected function setUp(): void {
        $this->repository = new OrderInMemoryRepository;
        $this->dispatchedEvents = [];

        $eventDispatcher = new class($this->dispatchedEvents) implements EventDispatcher {
            /** @param DomainEvent[] $events */
            public function __construct(private array &$events) {}
            public function dispatch(DomainEvent $event): void { $this->events[] = $event; }
        };

        $this->handler = new CreateOrderHandler(
            $this->repository,
            $eventDispatcher,
            new NullTransactionManager,
        );
    }

    #[Test]
    public function creates_order(): void {
        $command = new CreateOrder(id: Uuid::generate()->value(), ...);

        $result = ($this->handler)($command);

        $this->assertSame(Status::DRAFT, $result->status());
        $this->assertCount(1, $this->dispatchedEvents);
        $this->assertInstanceOf(OrderCreated::class, $this->dispatchedEvents[0]);
    }
}
```

Key patterns:
- `setUp()` creates InMemory repo + anonymous `EventDispatcher` + `NullTransactionManager`
- Anonymous class with `&$events` reference to capture dispatched events
- Assert on domain entity state + dispatched events

## InMemory Repository Pattern

```php
final class UserInMemoryRepository implements UserRepository {
    /** @var array<string, User> */
    private array $users = [];

    public function save(User $user): void {
        $this->users[$user->id()->value()] = $user;
    }

    public function findById(UserId $id): ?User {
        return $this->users[$id->value()] ?? null;
    }
}
```

Located at `modules/{Module}/Infrastructure/Persistence/InMemory/`.

## Feature Test Pattern

```php
final class OrderControllerTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        // Create test data via domain factories
    }

    #[Test]
    public function shows_order_page(): void {
        $this->actingAs($this->user)
            ->get('/orders')
            ->assertOk();
    }
}
```

Uses `RefreshDatabase` trait for real DB interaction.

## Debugging Failing Tests

When tests fail:

1. Read the error message carefully
2. Check test isolation (no shared state)
3. Verify mock implementations
4. Change production code, not tests (unless tests are wrong)
5. Use `/tdd-cycle` command for guided assistance

## What to Test

| Layer | Test Focus |
|-------|------------|
| Domain | Value object validation, entity invariants, domain services |
| Application | Handler behavior, input validation, output mapping |
| Infrastructure | Repository queries, external service integration |

## What NOT to Test

- Framework code (Laravel handles its own testing)
- Simple getters/setters without logic
- Private methods (test via public interface)
- Third-party library internals

## Coverage Exclusions

Models, Controllers, Form Requests, Resources, InMemory repos, and Service Providers are excluded from code coverage (configured in `phpunit.xml`).
