# Laravel Modular Monolith Architecture Skill

## Activation Triggers
- Creating or modifying files in `modules/`
- Discussing architecture, layers, or dependency direction
- Questions about where code should live

## Module Structure

Each module is self-contained with its own hexagonal layers:

```
modules/
├── {Module}/                      # e.g., User, Order, Product
│   ├── Domain/                    # Pure PHP, no Laravel deps
│   │   ├── Entity/                # Aggregates and entities
│   │   ├── ValueObject/           # Immutable value types
│   │   ├── Repository/            # Interfaces only
│   │   ├── Service/               # Domain services
│   │   ├── Event/                 # Domain events
│   │   └── Exception/             # Domain exceptions
│   ├── Application/               # Use cases
│   │   ├── Command/               # Write: DTO + Handler
│   │   └── Query/                 # Read: DTO + Handler
│   └── Infrastructure/            # Laravel implementations
│       ├── Persistence/
│       │   ├── Eloquent/
│       │   │   ├── Model/         # Eloquent models
│       │   │   └── Repository/    # Repository implementations
│       │   └── InMemory/          # For tests
│       ├── Http/
│       │   ├── Controller/
│       │   ├── Request/           # Form requests
│       │   └── Resource/          # API resources
│       └── Provider/              # Module service provider
```

## Layer Rules

### Domain Layer (`modules/{Module}/Domain/`)
**ALLOWED:**
- Pure PHP classes
- PHP standard library
- Other Domain classes within the same module

**FORBIDDEN:**
- Laravel facades (`DB`, `Cache`, `Log`, etc.)
- Eloquent models or collections
- HTTP concerns (Request, Response)
- Any `Illuminate\*` namespace

### Application Layer (`modules/{Module}/Application/`)
**ALLOWED:**
- Domain layer dependencies
- Other Application classes
- DTOs (Commands, Queries)

**FORBIDDEN:**
- Infrastructure concerns
- Direct database access
- HTTP Request/Response objects
- Laravel facades

### Infrastructure Layer (`modules/{Module}/Infrastructure/`)
**ALLOWED:**
- All Laravel features
- Domain and Application dependencies
- External packages

**FORBIDDEN:**
- Business logic (belongs in Domain)
- Orchestration logic (belongs in Application)

## File Templates

> Full templates available via `/create-entity`, `/create-value-object`, `/create-use-case`, `/create-repository`, `/create-controller` commands.

### Quick Reference

| Component | Location | Key Pattern |
|-----------|----------|-------------|
| Entity | `Domain/Entity/{Name}.php` | `final readonly`, private constructor, `create()` factory |
| Value Object | `Domain/ValueObject/{Name}.php` | `final readonly`, `fromString()`, `equals()` |
| Repository | `Domain/Repository/{Name}Repository.php` | Interface with `save()`, `findById()`, `delete()` |
| Command/Query | `Application/{Type}/{Name}.php` | `final readonly` DTO with public properties |
| Handler | `Application/{Type}/{Name}Handler.php` | `__invoke()` method, inject interfaces |
| Event | `Domain/Event/{Name}.php` | `final readonly`, `raise()` factory |
| Exception | `Domain/Exception/{Name}.php` | Named constructors: `withId()`, `empty()`, `withReason()` |
| Controller | `Infrastructure/Http/Controller/` | Thin, delegates to handlers |
| ServiceProvider | `Infrastructure/Provider/` | Binds interfaces to implementations |

### Exception Patterns
```php
// Not found
final class {Entity}NotFound extends \DomainException {
    public static function withId(string $id): self {
        return new self("{Entity} with ID {$id} was not found");
    }
}

// Validation
final class Invalid{Name} extends \DomainException {
    public static function empty(): self { return new self('{Name} cannot be empty'); }
    public static function withFormat(string $value): self { return new self("Invalid format: {$value}"); }
}
```

## Dependency Injection

Register module service provider in `config/app.php`:

```php
'providers' => [
    // ...
    Modules\User\Infrastructure\Provider\UserServiceProvider::class,
    Modules\Order\Infrastructure\Provider\OrderServiceProvider::class,
],
```

## Inter-Module Communication

### Direct Dependency (for simple cases)
```php
// Order module uses User module's interface
use Modules\User\Domain\Repository\UserRepository;
```

### Event-Based (for loose coupling)
```php
// User module publishes
$this->events->dispatch(new UserCreated($user->id()));

// Order module listens
class CreateWelcomeOrderOnUserCreated
{
    public function __invoke(UserCreated $event): void
    {
        // Handle
    }
}
```

## Common Mistakes to Avoid

1. **Using Eloquent in Domain**: Never `use Illuminate\Database\Eloquent\*` in Domain
2. **Returning Eloquent from Repository**: Convert to domain entities
3. **Business Logic in Controller**: Keep controllers thin
4. **Injecting Implementations**: Always inject interfaces
5. **Skipping Value Objects**: Use them for validation and type safety
6. **Cross-module Eloquent access**: Don't import another module's Eloquent models
7. **Circular dependencies**: Modules shouldn't have circular imports
