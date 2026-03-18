# Laravel Claude Toolkit

Modular Monolith with Hexagonal Architecture, DDD, and TDD.

## Quick Commands

```bash
sail composer test              # Full suite: analyze + parallel tests
sail composer test:fast         # Skip analysis, just run parallel tests
sail composer test:unit         # Unit tests only
sail composer test:integration  # Integration tests only
sail composer test:feature      # Feature tests only
npm run test                    # Frontend tests
npm run test:watch              # Frontend watch mode
```

## Module Namespace

All module code uses the `Modules\` namespace:
- Domain: `Modules\{Module}\Domain\Entity\{Name}`
- Application: `Modules\{Module}\Application\Command\{Action}{Entity}`
- Infrastructure: `Modules\{Module}\Infrastructure\Http\Controller\{Name}Controller`

## File Naming

| Type | Pattern | Example |
|------|---------|---------|
| Entity / Value Object | `{Name}.php` | `User.php`, `Email.php` |
| Entity ID | `{Name}Id.php` | `UserId.php` |
| Exception | `Invalid{Name}.php` / `{Name}NotFound.php` | `InvalidEmail.php` |
| Repository Interface | `{Name}Repository.php` | `UserRepository.php` |
| Eloquent Repository | `{Name}EloquentRepository.php` | `UserEloquentRepository.php` |
| Command / Query | `{Action}{Entity}.php` | `CreateUser.php` |
| Handler | `{Action}{Entity}Handler.php` | `CreateUserHandler.php` |
| Controller | `{Name}Controller.php` | `UserController.php` |

## New Feature Workflow

1. `/create-module` → `/create-value-object` → `/create-entity` → `/create-repository` → `/create-use-case` → `/create-controller`
2. Run `/test` to verify
3. `/update-changelog`

## Architecture

Rules in `.claude/rules/` are auto-loaded by file glob. Skills, commands, and agents in `.claude/` provide scaffolding and review capabilities.
