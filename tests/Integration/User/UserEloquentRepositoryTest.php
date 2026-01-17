<?php

declare(strict_types=1);

namespace Tests\Integration\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Domain\ValueObject\Email;
use Modules\User\Domain\Entity\User;
use Modules\User\Domain\Entity\UserId;
use Modules\User\Infrastructure\Persistence\Eloquent\Repository\UserEloquentRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserEloquentRepository;
    }

    #[Test]
    public function it_saves_and_retrieves_a_user(): void
    {
        $id = UserId::generate();
        $user = User::create(
            $id,
            'John Doe',
            Email::fromString('john@example.com')
        );

        $this->repository->save($user);
        $retrieved = $this->repository->findById($id);

        $this->assertNotNull($retrieved);
        $this->assertTrue($id->equals($retrieved->id()));
        $this->assertSame('John Doe', $retrieved->name());
        $this->assertSame('john@example.com', $retrieved->email()->value());
    }

    #[Test]
    public function it_returns_null_for_non_existent_user(): void
    {
        $id = UserId::generate();

        $result = $this->repository->findById($id);

        $this->assertNull($result);
    }

    #[Test]
    public function it_updates_existing_user(): void
    {
        $id = UserId::generate();
        $user = User::create(
            $id,
            'John Doe',
            Email::fromString('john@example.com')
        );
        $this->repository->save($user);

        $updatedUser = User::create(
            $id,
            'John Updated',
            Email::fromString('john.updated@example.com')
        );
        $this->repository->save($updatedUser);

        $retrieved = $this->repository->findById($id);

        $this->assertNotNull($retrieved);
        $this->assertSame('John Updated', $retrieved->name());
        $this->assertSame('john.updated@example.com', $retrieved->email()->value());
    }
}
