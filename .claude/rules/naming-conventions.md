---
description: File naming, namespace conventions, and feature creation workflow
---

# Naming Conventions

## File Naming

| Type | Pattern | Example |
|------|---------|---------|
| Entity | `{Name}.php` | `User.php` |
| Entity ID | `{Name}Id.php` | `UserId.php` |
| Value Object | `{Name}.php` | `Email.php` |
| Validation Exception | `Invalid{Name}.php` | `InvalidEmail.php` |
| Not Found Exception | `{Name}NotFound.php` | `UserNotFound.php` |
| Repository Interface | `{Name}Repository.php` | `UserRepository.php` |
| Eloquent Repository | `{Name}EloquentRepository.php` | `UserEloquentRepository.php` |
| Command | `{Action}{Entity}.php` | `CreateUser.php` |
| Command Handler | `{Action}{Entity}Handler.php` | `CreateUserHandler.php` |
| Controller | `{Name}Controller.php` | `UserController.php` |

## Namespace Convention

All module code uses the `Modules\` namespace:

- Domain: `Modules\User\Domain\Entity\User`
- Application: `Modules\User\Application\Command\CreateUser`
- Infrastructure: `Modules\User\Infrastructure\Http\Controller\UserController`

## Creating a New Feature

1. Scaffold module structure if new (`/create-module`)
2. Create value objects for domain concepts (`/create-value-object`)
3. Start with domain entity test (`/create-entity`)
4. Create repository interface and test (`/create-repository`)
5. Create use case handler and test (`/create-use-case`)
6. Create controller and feature test (`/create-controller`)
7. Run all tests to verify (`composer test`)
8. Update changelog (`/update-changelog`)
