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

### Entity (`modules/{Module}/Domain/Entity/{Name}.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Domain\Entity;

final readonly class {Name}
{
    private function __construct(
        private {Name}Id $id,
    ) {
    }

    public static function create({Name}Id $id): self
    {
        return new self($id);
    }

    public function id(): {Name}Id
    {
        return $this->id;
    }
}
```

### Value Object (`modules/{Module}/Domain/ValueObject/{Name}.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Domain\ValueObject;

final readonly class {Name}
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        // Validate here
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

### Repository Interface (`modules/{Module}/Domain/Repository/{Name}Repository.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Domain\Repository;

use Modules\{Module}\Domain\Entity\{Name};
use Modules\{Module}\Domain\Entity\{Name}Id;

interface {Name}Repository
{
    public function save({Name} $entity): void;
    public function findById({Name}Id $id): ?{Name};
    public function delete({Name}Id $id): void;
}
```

### Command DTO (`modules/{Module}/Application/Command/{Name}.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Application\Command;

final readonly class {Name}
{
    public function __construct(
        public string $id,
    ) {
    }
}
```

### Command Handler (`modules/{Module}/Application/Command/{Name}Handler.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Application\Command;

use Modules\{Module}\Domain\Repository\{Entity}Repository;

final readonly class {Name}Handler
{
    public function __construct(
        private {Entity}Repository $repository,
    ) {
    }

    public function __invoke({Name} $command): void
    {
        // Implementation
    }
}
```

### Controller (`modules/{Module}/Infrastructure/Http/Controller/{Name}Controller.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Infrastructure\Http\Controller;

use Modules\{Module}\Application\Command\{Command};
use Modules\{Module}\Application\Command\{Command}Handler;

final readonly class {Name}Controller
{
    public function __construct(
        private {Command}Handler $handler,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        ($this->handler)(new {Command}(
            id: $request->validated('id'),
        ));

        return response()->json(null, 201);
    }
}
```

### Module Service Provider (`modules/{Module}/Infrastructure/Provider/{Module}ServiceProvider.php`)
```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Infrastructure\Provider;

use Modules\{Module}\Domain\Repository\{Name}Repository;
use Modules\{Module}\Infrastructure\Persistence\Eloquent\Repository\{Name}EloquentRepository;
use Illuminate\Support\ServiceProvider;

final class {Module}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            {Name}Repository::class,
            {Name}EloquentRepository::class
        );
    }
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
