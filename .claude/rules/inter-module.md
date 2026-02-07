---
globs: modules/**/*.php
---

# Inter-Module Communication

## Communication Strategies

| Strategy | When to Use | Example |
|----------|-------------|---------|
| **Interface Injection** | Cross-module queries, synchronous reads | Task handler injects TeamRepository |
| **Domain Events** | Write side effects, eventual consistency | UserCreated -> Update team stats |
| **Shared Kernel** | Common value objects, IDs | `Modules\Shared\Domain\ValueObject\Uuid` |

## Never Allow

- Circular dependencies (Team -> Task -> Team)
- Infrastructure layer cross-references
- Direct model imports across modules

## Avoiding Circular Dependencies

| Strategy | When to Use |
|----------|-------------|
| **Dependency Inversion** | Module A needs B's data AND B needs A's data |
| **Domain Events** | Reacting to changes without coupling |
| **Shared Query Service** | Complex cross-module aggregations |
| **ID-Only References** | Store IDs, query separately when needed |

### Dependency Inversion Pattern

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

## Decision Matrix

| Scenario | Strategy |
|----------|----------|
| Module A needs data from B | Inject B's repository interface |
| Module A reacts to B's changes | Domain Events |
| Both A and B need each other | Dependency Inversion |
| Complex aggregation | Shared Query Service |
| Just need ID reference | ID-Only References |
