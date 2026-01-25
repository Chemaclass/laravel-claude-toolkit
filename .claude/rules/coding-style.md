# Coding Style Rules

## Core Principles

### Immutability First
```php
// BAD - mutable
class User {
    public string $email;
    public function setEmail(string $email): void {
        $this->email = $email;
    }
}

// GOOD - immutable
final readonly class User {
    private function __construct(
        public Email $email,
    ) {}

    public static function create(Email $email): self {
        return new self($email);
    }

    public function withEmail(Email $email): self {
        return new self($email);
    }
}
```

### File Size Limits

| Metric | Limit |
|--------|-------|
| Lines per file | 200-400 typical, 500 max |
| Lines per method | 20 max |
| Parameters per method | 3 max (use objects for more) |
| Nesting depth | 3 levels max |

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Classes | PascalCase, noun | `UserRepository`, `EmailValidator` |
| Methods | camelCase, verb | `findById()`, `validateEmail()` |
| Variables | camelCase, descriptive | `$expirationDays`, `$activeUsers` |
| Constants | SCREAMING_SNAKE | `MAX_RETRY_COUNT` |
| Interfaces | PascalCase, noun | `UserRepository` (no I prefix) |

### Error Handling

```php
// BAD - silent failure
public function findUser(string $id): ?User {
    try {
        return $this->repository->find($id);
    } catch (Exception) {
        return null; // Lost context
    }
}

// GOOD - explicit handling
public function findUser(UserId $id): ?User {
    return $this->repository->find($id);
}

public function getUser(UserId $id): User {
    return $this->repository->find($id)
        ?? throw UserNotFound::withId($id);
}
```

## Pre-Completion Checklist

Before considering code complete:

- [ ] Code is readable with meaningful names
- [ ] Functions are under 20 lines
- [ ] Files are under 500 lines
- [ ] Nesting is under 4 levels
- [ ] Errors are handled explicitly
- [ ] No `dd()`, `dump()`, or debug statements
- [ ] No hardcoded values (use config/constants)
- [ ] Immutable patterns used where applicable
- [ ] Types declared for all parameters and returns
