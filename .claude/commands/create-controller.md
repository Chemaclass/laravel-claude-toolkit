# Create Controller

Create a thin HTTP controller with feature test.

## Arguments
- `$ARGUMENTS` - Format: `<Module> <ControllerName>` (e.g., `User UserController`, `Order OrderController`)

## Instructions

1. **Parse arguments**: Extract module and controller name from `$ARGUMENTS`

2. **Create the feature test first** (TDD):
   ```
   tests/Feature/<Module>/<ControllerName>Test.php
   ```
   - Test HTTP endpoints
   - Test request validation
   - Test response structure
   - Use RefreshDatabase trait

3. **Run the test** to see it fail (Red phase):
   ```bash
   ./vendor/bin/sail test --filter <ControllerName>Test
   ```

4. **Create the Form Request** (if needed):
   ```
   modules/<Module>/Infrastructure/Http/Request/<ActionName>Request.php
   ```
   - Validation rules
   - Authorization logic

5. **Create the API Resource** (if needed):
   ```
   modules/<Module>/Infrastructure/Http/Resource/<EntityName>Resource.php
   ```
   - Transform entity to JSON response

6. **Create the Controller**:
   ```
   modules/<Module>/Infrastructure/Http/Controller/<ControllerName>.php
   ```
   - Keep it thin: validate, dispatch, respond
   - Inject handlers, not repositories
   - Use Form Requests for validation
   - Use Resources for response transformation

7. **Register routes** in module routes file or `routes/api.php`

8. **Run the test again** to see it pass (Green phase)

## Controller Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Infrastructure\Http\Controller;

use Modules\<Module>\Application\Command\Create<Entity>;
use Modules\<Module>\Application\Command\Create<Entity>Handler;
use Modules\<Module>\Application\Query\Get<Entity>ById;
use Modules\<Module>\Application\Query\Get<Entity>ByIdHandler;
use Modules\<Module>\Infrastructure\Http\Request\Create<Entity>Request;
use Modules\<Module>\Infrastructure\Http\Resource\<Entity>Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final readonly class <ControllerName>
{
    public function __construct(
        private Create<Entity>Handler $createHandler,
        private Get<Entity>ByIdHandler $getByIdHandler,
    ) {
    }

    public function store(Create<Entity>Request $request): JsonResponse
    {
        ($this->createHandler)(new Create<Entity>(
            id: $request->validated('id'),
            // Map other fields
        ));

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $entity = ($this->getByIdHandler)(new Get<Entity>ById($id));

        if ($entity === null) {
            return response()->json(
                ['error' => '<Entity> not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(
            new <Entity>Resource($entity)
        );
    }
}
```

## Form Request Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class Create<Entity>Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'uuid'],
            // Add other validation rules
        ];
    }
}
```

## API Resource Template

```php
<?php

declare(strict_types=1);

namespace Modules\<Module>\Infrastructure\Http\Resource;

use Modules\<Module>\Domain\Entity\<Entity>;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property <Entity> $resource */
final class <Entity>Resource extends JsonResource
{
    public function __construct(<Entity> $entity)
    {
        parent::__construct($entity);
    }

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id()->toString(),
            // Map other properties
        ];
    }
}
```

## Feature Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\<Module>;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class <ControllerName>Test extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_<entity>(): void
    {
        $response = $this->postJson('/api/<entities>', [
            'id' => 'test-uuid',
            // Add other fields
        ]);

        $response->assertCreated();
    }

    public function test_can_get_<entity>_by_id(): void
    {
        // Arrange: create entity first
        $this->postJson('/api/<entities>', [
            'id' => 'test-uuid',
        ]);

        // Act
        $response = $this->getJson('/api/<entities>/test-uuid');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'id',
        ]);
    }

    public function test_returns_404_when_<entity>_not_found(): void
    {
        $response = $this->getJson('/api/<entities>/non-existent');

        $response->assertNotFound();
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->postJson('/api/<entities>', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }
}
```

## Error Handling Pattern

```php
public function store(Create<Entity>Request $request): JsonResponse
{
    try {
        ($this->handler)(new Create<Entity>(id: $request->validated('id')));
        return response()->json(null, Response::HTTP_CREATED);
    } catch (Invalid<Entity> $e) {
        return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

public function show(string $id): JsonResponse
{
    try {
        $entity = ($this->getByIdHandler)(new Get<Entity>ById($id));
        return response()->json(new <Entity>Resource($entity));
    } catch (<Entity>NotFound $e) {
        return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
    }
}
```

## Test Base Class Note

Feature tests use `Tests\TestCase` with `RefreshDatabase` trait for HTTP testing helpers.

## Checklist
- [ ] Feature test created first
- [ ] Test fails initially (Red)
- [ ] Form Request created (if needed)
- [ ] API Resource created (if needed)
- [ ] Controller created (thin, delegates to handlers)
- [ ] Domain exception handling added
- [ ] Routes registered
- [ ] Test passes (Green)
- [ ] No business logic in controller
