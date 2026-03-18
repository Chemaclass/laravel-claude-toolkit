# Quick Test Runner

Run tests with smart filtering.

## Arguments
- `$ARGUMENTS` - Optional: Test filter (class name, method, or module path)

## Instructions

1. If `$ARGUMENTS` is empty, run all tests:
```bash
   sail composer test
```

2. If `$ARGUMENTS` looks like a module name (e.g., `User`, `Order`):
```bash
   ./vendor/bin/sail test tests/Unit/$ARGUMENTS tests/Integration/$ARGUMENTS tests/Feature/$ARGUMENTS
```

3. If `$ARGUMENTS` looks like a test class or method:
```bash
   ./vendor/bin/sail test --filter $ARGUMENTS
```

4. Report results clearly with pass/fail count.
