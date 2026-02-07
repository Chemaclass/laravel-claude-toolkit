---
globs: modules/*/Domain/**/*.php
---

# Domain Layer Conventions

The Domain layer is **pure PHP** with zero Laravel dependencies.

## Core Rules

- Use `final readonly class` for entities and value objects
- Private constructor + static factory method (`create()`, `fromString()`)
- Validate invariants in factory methods
- No Laravel dependencies whatsoever
- Repository interfaces live here (implementations in Infrastructure)

## Entity Pattern

```php
final class User {
    private function __construct(
        private readonly UserId $id,
        private UserName $name,
    ) {}

    // For NEW entities — validates invariants, sets defaults
    public static function create(UserId $id, UserName $name): self {
        return new self($id, $name);
    }

    // For LOADING from persistence — raw hydration, skips validation
    public static function reconstitute(UserId $id, UserName $name): self {
        return new self($id, $name);
    }

    public function id(): UserId { return $this->id; }
    public function updateName(UserName $name): void { $this->name = $name; }
}
```

- `create()` — factory for new entities with business logic and defaults
- `reconstitute()` — factory for hydration from DB (already-validated data)
- Getter methods for all properties, update methods for mutable ones

## Entity IDs

All IDs extend `Modules\Shared\Domain\ValueObject\Uuid`:

```php
final readonly class UserId extends Uuid {}
```

No custom logic — just type differentiation.

## Value Objects

```php
final readonly class Email implements \Stringable {
    private function __construct(private string $value) {}

    public static function fromString(string $value): self {
        $value = trim($value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: $value");
        }
        return new self($value);
    }

    public function value(): string { return $this->value; }
    public function equals(Email $other): bool { return $this->value === $other->value; }
    public function __toString(): string { return $this->value; }
}
```

- Implement `Stringable` with `__toString()`
- `value()` accessor + `equals()` for comparison
- Validation in factory, throws `InvalidArgumentException`

## Enum Value Objects

Use backed enums for domain concepts with behavior:

```php
enum Status: string {
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function fromString(string $value): self { return self::from($value); }
    public function label(): string { return match ($this) { self::DRAFT => 'Draft', ... }; }
    public function isClosed(): bool { return $this === self::COMPLETED || $this === self::CANCELLED; }
}
```

## Domain Events

```php
final readonly class UserCreated implements DomainEvent {
    public function __construct(
        private User $user,
        private DateTimeImmutable $occurredOn,
        private ?string $createdBy = null,
    ) {}

    public static function fromUser(User $user, ?string $createdBy = null): self {
        return new self($user, new DateTimeImmutable, $createdBy);
    }

    public function occurredOn(): DateTimeImmutable { return $this->occurredOn; }
}
```

- Implement `Modules\Shared\Domain\Event\DomainEvent` (requires `occurredOn()`)
- `final readonly class` — immutable after creation
- Static factory from aggregate entity

## Exceptions

```php
final class UserNotFound extends \RuntimeException {
    public static function withId(UserId $id): self {
        return new self("User with ID \"{$id->value()}\" not found");
    }
}

final class InvalidEmail extends \DomainException {
    public static function empty(): self { return new self('Email cannot be empty'); }
}
```

- Named static constructors with context (`withId()`, `empty()`, `withFormat()`)
- Extend `RuntimeException` (not found) or `DomainException` (validation)

## Cross-Module References

- Store IDs only, never full entities from other modules
- Use `Modules\Shared\Domain\ValueObject\*` for common VOs (Email, Uuid, Address)
