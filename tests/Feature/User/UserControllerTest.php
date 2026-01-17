<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Domain\ValueObject\Email;
use Modules\User\Domain\Entity\User;
use Modules\User\Domain\Entity\UserId;
use Modules\User\Infrastructure\Persistence\Eloquent\Repository\UserEloquentRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
            ])
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.email', 'john@example.com');
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_retrieves_a_user_by_id(): void
    {
        $repository = new UserEloquentRepository;
        $id = UserId::generate();
        $user = User::create(
            $id,
            'John Doe',
            Email::fromString('john@example.com')
        );
        $repository->save($user);

        $response = $this->getJson("/api/users/{$id->value()}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $id->value())
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.email', 'john@example.com');
    }

    #[Test]
    public function it_returns_404_for_non_existent_user(): void
    {
        $nonExistentId = UserId::generate()->value();

        $response = $this->getJson("/api/users/{$nonExistentId}");

        $response->assertStatus(404)
            ->assertJsonPath('error', "User not found with ID: {$nonExistentId}");
    }
}
