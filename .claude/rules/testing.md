# Testing Rules

## Coverage Standards

- **Minimum 80% coverage** for all new code
- Domain layer should have **95%+ coverage**
- Critical business logic requires **100% coverage**

## Required Test Types

| Type | Location | Purpose |
|------|----------|---------|
| Unit Tests | `tests/Unit/{Module}/` | Individual classes, value objects, entities |
| Integration Tests | `tests/Integration/{Module}/` | Repository implementations, database operations |
| Feature Tests | `tests/Feature/{Module}/` | HTTP endpoints, full request lifecycle |

## TDD Workflow

Always follow red-green-refactor:

1. **RED**: Write failing test first
2. **GREEN**: Write minimal code to pass
3. **REFACTOR**: Improve while tests stay green
4. **VERIFY**: Check coverage with `./vendor/bin/phpunit --coverage-text`

## Test Naming Convention

```php
// Method: test_<what>_<condition>_<expected>
public function test_create_user_with_valid_email_returns_user(): void
public function test_create_user_with_invalid_email_throws_exception(): void

// Or use descriptive it_* naming
public function it_creates_a_user_with_valid_data(): void
public function it_throws_when_email_is_invalid(): void
```

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
