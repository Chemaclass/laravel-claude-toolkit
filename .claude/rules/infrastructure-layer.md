---
globs: modules/*/Infrastructure/**/*.php
---

# Infrastructure Layer Conventions

The Infrastructure layer provides Laravel implementations of domain interfaces.

## Core Rules

- Thin controllers (validate, dispatch, respond)
- Use Form Requests for validation
- Use API Resources for response transformation
- Register interface bindings in module service providers
- Controllers should never use Eloquent models to talk to DB directly; use application services with the repository pattern

## Eloquent Repository Pattern

```php
final readonly class UserEloquentRepository implements UserRepository {
    public function save(User $user): void {
        UserModel::query()->updateOrCreate(
            ['id' => $user->id()->value()],
            ['name' => $user->name(), 'email' => $user->email()->value()],
        );
    }

    public function findById(UserId $id): ?User {
        $model = UserModel::query()->find($id->value());
        return $model === null ? null : $this->toEntity($model);
    }

    private function toEntity(UserModel $model): User {
        return User::reconstitute(
            id: UserId::fromString($model->id),
            name: $model->name,
            email: Email::fromString($model->email),
        );
    }
}
```

- `final readonly class` implementing domain interface
- `toEntity()` private method hydrates model to domain entity via `reconstitute()`
- Extract IDs with `->value()`, persist with `updateOrCreate()`
- Always use `::query()` before Eloquent query methods

## Eloquent Model Conventions

```php
final class OrderModel extends Model {
    use HasUuids;

    protected $table = 'orders';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'number', 'user_id', ...];

    protected function casts(): array {
        return ['items' => 'array', 'expires_at' => 'datetime'];
    }

    /** @return BelongsTo<UserModel, $this> */
    public function user(): BelongsTo {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
```

- `final class` + `HasUuids` trait (UUID primary keys)
- Use `casts()` method (not `$casts` property)
- Include generic docblocks on relationships: `@return BelongsTo<Model, $this>`
- Keep models thin — no business logic, only Eloquent concerns
- Models are excluded from code coverage

## Controller Pattern

```php
final readonly class UserController {
    public function __construct(
        private CreateUserHandler $createHandler,
        private GetUserByIdHandler $getHandler,
    ) {}

    public function store(CreateUserRequest $request): JsonResponse {
        $id = Uuid::generate()->value();
        ($this->createHandler)(new CreateUser(id: $id, ...));
        $user = ($this->getHandler)(new GetUserById($id));
        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
```

- `final readonly class` with constructor-injected handlers
- Invoke handlers via `($this->handler)($dto)`
- Use Form Requests for validation, Resources for response

## Dual Web/API Controllers

When a controller serves both Inertia (web) and AJAX (JSON) requests:

```php
public function store(Request $request): JsonResponse|RedirectResponse {
    // ... create entity ...
    if ($request->wantsJson()) {
        return response()->json(['id' => $id, 'name' => $name], 201);
    }
    return redirect()->route('entity.show', $id);
}
```

Use `$request->wantsJson()` to return JSON for axios/fetch calls.

## Module Routes

Each module declares routes in `Infrastructure/Http/routes.php`, loaded by the ServiceProvider:

```php
// ServiceProvider boot()
public function boot(): void {
    $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
}

// routes.php
Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/users', UserDashboardController::class)->name('user.dashboard');
});
```

- Web routes use `['web', 'auth']` middleware
- API routes use `api/` prefix
- Single-action controllers use invokable syntax: `Controller::class`

## Service Provider

```php
final class UserServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(UserRepository::class, UserEloquentRepository::class);
    }

    public function boot(): void {
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
    }
}
```

- `register()` — bind domain interfaces to Eloquent implementations
- `boot()` — load module routes
- Register in `bootstrap/providers.php`
