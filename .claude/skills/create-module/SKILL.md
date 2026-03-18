# Create Module

Scaffold a new module with the full hexagonal architecture directory structure.

## Arguments
- `$ARGUMENTS` - Format: `<ModuleName>` (e.g., `User`, `Order`, `Payment`)

## Instructions

1. **Parse arguments**: Extract module name from `$ARGUMENTS`

2. **Create directory structure**:
   ```
   modules/<ModuleName>/
   ├── Domain/
   │   ├── Entity/
   │   ├── ValueObject/
   │   ├── Repository/
   │   ├── Service/
   │   ├── Event/
   │   └── Exception/
   ├── Application/
   │   ├── Command/
   │   └── Query/
   └── Infrastructure/
       ├── Persistence/
       │   ├── Eloquent/
       │   │   ├── Model/
       │   │   └── Repository/
       │   └── InMemory/
       ├── Http/
       │   ├── Controller/
       │   ├── Request/
       │   └── Resource/
       └── Provider/
   ```

3. **Create the Module Service Provider**:
   ```
   modules/<ModuleName>/Infrastructure/Provider/<ModuleName>ServiceProvider.php
   ```

4. **Create test directory structure**:
   ```
   tests/
   ├── Unit/<ModuleName>/
   │   ├── Domain/
   │   │   ├── Entity/
   │   │   └── ValueObject/
   │   └── Application/
   │       ├── Command/
   │       └── Query/
   ├── Integration/<ModuleName>/
   └── Feature/<ModuleName>/
   ```

5. **Register the module** in `config/app.php` or `bootstrap/providers.php`

6. **Add .gitkeep files** to empty directories (optional)

## Service Provider Template

```php
<?php
declare(strict_types=1);

namespace Modules\<ModuleName>\Infrastructure\Provider;

use Illuminate\Support\ServiceProvider;

final class <ModuleName>ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // $this->app->bind(InterfaceRepository::class, EloquentRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../Http/Routes/api.php');
    }
}
```

## Setup Commands

```bash
# Create directories
mkdir -p modules/<ModuleName>/Domain/{Entity,ValueObject,Repository,Service,Event,Exception}
mkdir -p modules/<ModuleName>/Application/{Command,Query}
mkdir -p modules/<ModuleName>/Infrastructure/{Persistence/Eloquent/{Model,Repository},Persistence/InMemory,Http/{Controller,Request,Resource,Routes},Provider,Database/Migrations}
mkdir -p tests/{Unit/<ModuleName>/Domain/{Entity,ValueObject},Unit/<ModuleName>/Application/{Command,Query},Integration/<ModuleName>,Feature/<ModuleName>}

# Add .gitkeep files
find modules/<ModuleName> tests/Unit/<ModuleName> tests/Integration/<ModuleName> tests/Feature/<ModuleName> -type d -empty -exec touch {}/.gitkeep \;

# Ensure composer.json has: "Modules\\": "modules/"
composer dump-autoload
```

## Registration

**Laravel 11+** (`bootstrap/providers.php`):
```php
Modules\<ModuleName>\Infrastructure\Provider\<ModuleName>ServiceProvider::class,
```

**Laravel 10-** (`config/app.php`):
```php
'providers' => [Modules\<ModuleName>\Infrastructure\Provider\<ModuleName>ServiceProvider::class],
```

## Next Steps

After creating the module structure, use these commands to add functionality:

1. `/create-entity <ModuleName> <EntityName>` - Create domain entity
2. `/create-value-object <ModuleName> <ValueObjectName>` - Create value object
3. `/create-repository <ModuleName> <EntityName>` - Create repository
4. `/create-use-case <ModuleName> Command <ActionEntity>` - Create command handler
5. `/create-use-case <ModuleName> Query <QueryName>` - Create query handler
6. `/create-controller <ModuleName> <ControllerName>` - Create HTTP controller

## Checklist
- [ ] Directory structure created
- [ ] Service Provider created
- [ ] Namespace added to composer.json (if not present)
- [ ] composer dump-autoload executed
- [ ] Service Provider registered in config/app.php or bootstrap/providers.php
- [ ] Routes file created (optional)
- [ ] Test directories created
- [ ] .gitkeep files added to empty directories
