# Laravel Claude Toolkit - Architecture Guide

This Laravel project follows a **Modular Monolith Architecture** where each module is self-contained with **Hexagonal Architecture** (Ports & Adapters), **Domain-Driven Design** tactical patterns, and **Test-Driven Development** practices.

## Architecture Overview

```
┌───────────────────────────────────────────────────────────────────────┐
│                              modules/                                 │
│  ┌─────────────────────────┐    ┌─────────────────────────┐           │
│  │        User Module      │    │       Order Module      │           │
│  │  ┌───────────────────┐  │    │  ┌───────────────────┐  │           │
│  │  │  Infrastructure   │  │    │  │  Infrastructure   │  │           │
│  │  │  ┌─────────────┐  │  │    │  │  ┌─────────────┐  │  │           │
│  │  │  │ Application │  │  │    │  │  │ Application │  │  │           │
│  │  │  │ ┌─────────┐ │  │  │    │  │  │ ┌─────────┐ │  │  │           │
│  │  │  │ │ Domain  │ │  │  │    │  │  │ │ Domain  │ │  │  │           │
│  │  │  │ └─────────┘ │  │  │    │  │  │ └─────────┘ │  │  │           │
│  │  │  └─────────────┘  │  │    │  │  └─────────────┘  │  │           │
│  │  └───────────────────┘  │    │  └───────────────────┘  │           │
│  └─────────────────────────┘    └─────────────────────────┘           │
└───────────────────────────────────────────────────────────────────────┘
```

### Dependency Rule
- **Domain** → No dependencies (pure PHP)
- **Application** → Depends only on Domain
- **Infrastructure** → Depends on Application & Domain
- **Inter-module** → Via interfaces or events

## Directory Structure

```
modules/                           # Each module is self-contained
├── {Module}/                      # e.g., User, Order, Product
│   ├── Domain/                    # Pure PHP, no Laravel deps
│   │   ├── Entity/                # Aggregates with identity
│   │   ├── ValueObject/           # Immutable value types
│   │   ├── Repository/            # Interfaces only
│   │   ├── Service/               # Domain services
│   │   └── Exception/             # Domain exceptions
│   ├── Application/               # Use cases
│   │   ├── Command/               # Write ops: DTO + Handler
│   │   └── Query/                 # Read ops: DTO + Handler
│   └── Infrastructure/            # Laravel implementations
│       ├── Persistence/
│       │   ├── Eloquent/
│       │   │   ├── Model/
│       │   │   └── Repository/
│       │   └── InMemory/          # For tests
│       ├── Http/
│       │   ├── Controller/
│       │   ├── Request/
│       │   └── Resource/
│       └── Provider/              # Module service provider

app/                               # Laravel app (global stuff only)

tests/
├── Unit/{Module}/                 # Per-module unit tests
│   ├── Domain/
│   └── Application/
├── Integration/{Module}/          # Per-module integration tests
└── Feature/{Module}/              # Per-module feature tests
```

## Coding Conventions

### Domain Layer
- Use `final readonly class` for entities and value objects
- Private constructor + static factory method (`create()`, `fromString()`)
- Validate invariants in factory methods
- No Laravel dependencies whatsoever

### Application Layer
- One handler per use case
- Use `__invoke()` for handlers
- Inject repository interfaces, not implementations
- Commands for writes, Queries for reads

### Infrastructure Layer
- Thin controllers (validate, dispatch, respond)
- Use Form Requests for validation
- Use API Resources for response transformation
- Register interface bindings in module service providers

## Inter-Module Communication

### Communication Strategies

| Strategy | When to Use | Example |
|----------|-------------|---------|
| **Interface Injection** | Cross-module queries, synchronous reads | Task handler injects TeamRepository |
| **Domain Events** | Write side effects, eventual consistency | UserCreated → Update team stats |
| **Shared Kernel** | Common value objects, IDs | `Modules\Shared\Domain\ValueObject\Uuid` |

### Dependency Rules

| From Module | Can Depend On |
|-------------|---------------|
| Task | Team (interface), User (IDs), Shared |
| Team | User (interface), Shared |
| User | Shared only |

**Never allow**:
- Circular dependencies (Team → Task → Team)
- Infrastructure layer cross-references
- Direct model imports across modules

### Cross-Module Query Example

When querying across modules, the module owning the result type owns the handler:

```php
// Task module queries Team module for user IDs
final readonly class GetTasksByTeamHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TeamRepository $teamRepository,  // From Team module
    ) {}

    public function __invoke(GetTasksByTeam $query): array
    {
        $team = $this->teamRepository->findById(TeamId::fromString($query->teamId));
        $userIds = $team->getUserIds();
        return $this->taskRepository->findByUserIds($userIds);
    }
}
```

### Avoiding Circular Dependencies

| Strategy | When to Use |
|----------|-------------|
| **Dependency Inversion** | Module A needs B's data AND B needs A's data |
| **Domain Events** | Reacting to changes without coupling |
| **Shared Query Service** | Complex cross-module aggregations |
| **ID-Only References** | Store IDs, query separately when needed |

**Dependency Inversion Pattern**:
```php
// Team defines what it needs (interface)
namespace Modules\Team\Domain\Contract;
interface TeamTaskCounter {
    public function countByTeamId(TeamId $teamId): int;
}

// Task implements it (adapter) - no circular dependency
namespace Modules\Task\Infrastructure\Adapter;
class TaskRepositoryTeamCounter implements TeamTaskCounter { ... }
```

### Decision Matrix

| Scenario | Strategy |
|----------|----------|
| Module A needs data from B | Inject B's repository interface |
| Module A reacts to B's changes | Domain Events |
| Both A and B need each other | Dependency Inversion |
| Complex aggregation | Shared Query Service |
| Just need ID reference | ID-Only References |

## TDD Workflow

Always follow red-green-refactor:

1. **RED**: Write failing test first
2. **GREEN**: Write minimum code to pass
3. **REFACTOR**: Improve while keeping tests green

```bash
# Run all tests
./vendor/bin/sail test

# Run by suite
./vendor/bin/sail test tests/Unit
./vendor/bin/sail test tests/Integration
./vendor/bin/sail test tests/Feature

# Run specific module tests
./vendor/bin/sail test tests/Unit/User
./vendor/bin/sail test --filter UserTest
```

## Available Commands

| Command | Description |
|---------|-------------|
| `/create-entity <Module> <Name>` | Create domain entity with test |
| `/create-use-case <Module> <Type> <Name>` | Create command/query handler |
| `/create-repository <Module> <Name>` | Create repo interface + implementations |
| `/create-controller <Module> <Name>` | Create thin HTTP controller |
| `/tdd-cycle` | Guide red-green-refactor workflow |
| `/refactor-check <path>` | Analyze code for SOLID violations |

## Available Agents

| Agent | Use For |
|-------|---------|
| `domain-architect` | DDD/modular monolith architecture guidance |
| `tdd-coach` | Test-driven development coaching |
| `clean-code-reviewer` | Code quality review |

## Quick Reference

### Creating a New Feature

1. Start with domain entity test (`/create-entity`)
2. Create repository interface and test (`/create-repository`)
3. Create use case handler and test (`/create-use-case`)
4. Create controller and feature test (`/create-controller`)
5. Run all tests to verify

### File Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Entity | `{Name}.php` | `User.php` |
| Entity ID | `{Name}Id.php` | `UserId.php` |
| Value Object | `{Name}.php` | `Email.php` |
| Repository Interface | `{Name}Repository.php` | `UserRepository.php` |
| Eloquent Repository | `{Name}EloquentRepository.php` | `UserEloquentRepository.php` |
| Command | `{Action}{Entity}.php` | `CreateUser.php` |
| Command Handler | `{Action}{Entity}Handler.php` | `CreateUserHandler.php` |
| Controller | `{Name}Controller.php` | `UserController.php` |

### Test Naming Conventions

| Test Type | File Pattern |
|-----------|--------------|
| Entity Test | `tests/Unit/{Module}/Domain/Entity/{Name}Test.php` |
| Handler Test | `tests/Unit/{Module}/Application/{Type}/{Name}HandlerTest.php` |
| Repository Test | `tests/Integration/{Module}/{Name}RepositoryTest.php` |
| Feature Test | `tests/Feature/{Module}/{Name}Test.php` |

### Namespace Convention

All module code uses the `Modules\` namespace:
- Domain: `Modules\User\Domain\Entity\User`
- Application: `Modules\User\Application\Command\CreateUser`
- Infrastructure: `Modules\User\Infrastructure\Http\Controller\UserController`
