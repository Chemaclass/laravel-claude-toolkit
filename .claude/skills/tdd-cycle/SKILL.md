# TDD Cycle Guide

Guide the red-green-refactor workflow for test-driven development.

## Arguments
- `$ARGUMENTS` - Optional: specific test file or class to work on

## The TDD Cycle

### 1. RED - Write a Failing Test

Before writing any production code:

1. **Identify the behavior** you want to implement
2. **Write the smallest test** that demonstrates that behavior
3. **Run the test** and verify it fails for the expected reason

```bash
./vendor/bin/sail test --filter <TestName>
```

**Good failing test reasons:**
- Class does not exist
- Method does not exist
- Assertion failed (expected vs actual mismatch)

**Bad failing test reasons:**
- Syntax error in test
- Wrong test setup
- Unrelated exception

### 2. GREEN - Make It Pass

Write the **minimum code** necessary to make the test pass:

1. **Don't over-engineer** - write just enough code
2. **It's okay to hardcode** values initially
3. **Don't add extra features** not covered by tests
4. **Run the test** and verify it passes

```bash
./vendor/bin/sail test --filter <TestName>
```

### 3. REFACTOR - Improve the Code

With passing tests as a safety net:

1. **Remove duplication** (DRY)
2. **Improve naming** for clarity
3. **Extract methods/classes** if needed
4. **Keep running tests** after each change

```bash
./vendor/bin/sail test
```

**Refactoring rules:**
- No new functionality
- Tests must stay green
- Small, incremental changes

## Test Types by Layer

| Layer | Location | Base Class | Purpose |
|-------|----------|------------|---------|
| Unit (Domain) | `tests/Unit/<Module>/Domain/` | `PHPUnit\TestCase` | Entities, VOs, domain services |
| Unit (Application) | `tests/Unit/<Module>/Application/` | `PHPUnit\TestCase` | Handlers with mocked repos |
| Integration | `tests/Integration/<Module>/` | `Tests\TestCase` | Repository implementations |
| Feature | `tests/Feature/<Module>/` | `Tests\TestCase` | HTTP request/response |

> See `tdd-workflow` skill for complete test templates.

## TDD Best Practices

- **Test naming**: `test_throws_exception_when_email_is_invalid()`
- **AAA pattern**: Arrange → Act → Assert
- **One concept per test**: Focus on single behavior
- **Mock at boundaries**: Mock interfaces, not concrete classes

## Running Tests

```bash
# Run all tests
./vendor/bin/sail test

# Run specific module tests
./vendor/bin/sail test tests/Unit/User
./vendor/bin/sail test tests/Integration/User
./vendor/bin/sail test tests/Feature/User

# Run tests matching filter
./vendor/bin/sail test --filter UserTest

# Run specific test method
./vendor/bin/sail test --filter test_can_create_user

# Run with coverage
./vendor/bin/sail test --coverage
```

## Checklist
- [ ] Test written before implementation
- [ ] Test fails for the right reason
- [ ] Minimum code written to pass
- [ ] Test passes
- [ ] Code refactored
- [ ] All tests still pass
