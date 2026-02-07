# Fix All Code Quality Issues

Auto-fix all code quality issues in sequence.

## Instructions

1. Run Pint for code style:
```bash
   sail composer lint:fix
```

2. Run Rector for refactoring:
```bash
   sail composer rector
```

3. Run PHPStan and report any remaining issues:
```bash
   sail composer phpstan
```

4. Run tests to verify nothing broke:
```bash
   sail composer test
```

5. Summarize what was fixed and any remaining issues.
