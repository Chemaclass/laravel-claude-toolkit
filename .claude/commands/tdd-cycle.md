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

### Unit Tests (Domain)
- **Location:** `tests/Unit/<Module>/Domain/`
- **Purpose:** Test entities, value objects, domain services
- **Dependencies:** None (pure PHP)
- **Speed:** Very fast

```php
final class UserTest extends TestCase  // NOT Laravel TestCase
{
    public function test_email_must_be_valid(): void
    {
        $this->expectException(InvalidEmailException::class);
        Email::fromString('invalid');
    }
}
```

### Unit Tests (Application)
- **Location:** `tests/Unit/<Module>/Application/`
- **Purpose:** Test command/query handlers
- **Dependencies:** Mocked repositories
- **Speed:** Fast

```php
final class CreateUserHandlerTest extends TestCase
{
    public function test_creates_user(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('save');

        $handler = new CreateUserHandler($repo);
        $handler(new CreateUser(id: 'id', email: 'a@b.com'));
    }
}
```

### Integration Tests
- **Location:** `tests/Integration/<Module>/`
- **Purpose:** Test repository implementations
- **Dependencies:** Real database
- **Speed:** Slower

```php
final class UserEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_persists_user(): void
    {
        $repo = new UserEloquentRepository();
        $user = User::create(UserId::generate(), Email::fromString('a@b.com'));

        $repo->save($user);

        $this->assertDatabaseHas('users', ['email' => 'a@b.com']);
    }
}
```

### Feature Tests (HTTP)
- **Location:** `tests/Feature/<Module>/`
- **Purpose:** Test full HTTP request/response cycle
- **Dependencies:** Full application
- **Speed:** Slowest

```php
final class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_user_via_api(): void
    {
        $response = $this->postJson('/api/users', [
            'email' => 'test@example.com',
        ]);

        $response->assertCreated();
    }
}
```

## TDD Best Practices

### Test Naming
Use descriptive names that explain the scenario:
```php
// Good
test_throws_exception_when_email_is_invalid()
test_can_create_user_with_valid_data()
test_returns_404_when_user_not_found()

// Bad
test_user()
test_create()
testException()
```

### Test Structure (AAA Pattern)
```php
public function test_something(): void
{
    // Arrange - set up the test data
    $user = User::create(...);

    // Act - perform the action
    $result = $user->changeName('New Name');

    // Assert - verify the outcome
    $this->assertEquals('New Name', $result->name());
}
```

### One Assertion Per Test
Prefer focused tests with single assertions:
```php
// Good - two separate tests
public function test_user_has_correct_email(): void { ... }
public function test_user_has_correct_name(): void { ... }

// Acceptable - related assertions in one test
public function test_user_is_created_correctly(): void {
    $this->assertEquals($email, $user->email());
    $this->assertEquals($name, $user->name());
}
```

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
