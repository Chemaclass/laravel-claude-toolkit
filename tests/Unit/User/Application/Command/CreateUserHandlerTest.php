<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Command;

use Modules\User\Application\Command\CreateUser;
use Modules\User\Application\Command\CreateUserHandler;
use Modules\User\Domain\Entity\UserId;
use Modules\User\Infrastructure\Persistence\InMemory\UserInMemoryRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateUserHandlerTest extends TestCase
{
    private UserInMemoryRepository $repository;

    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new UserInMemoryRepository;
        $this->handler = new CreateUserHandler($this->repository);
    }

    #[Test]
    public function it_creates_a_user(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $command = new CreateUser(
            id: $id,
            name: 'John Doe',
            email: 'john@example.com'
        );

        ($this->handler)($command);

        $user = $this->repository->findById(UserId::fromString($id));

        $this->assertNotNull($user);
        $this->assertSame($id, $user->id()->value());
        $this->assertSame('John Doe', $user->name());
        $this->assertSame('john@example.com', $user->email()->value());
    }

    #[Test]
    public function it_stores_user_in_repository(): void
    {
        $command = new CreateUser(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Jane Doe',
            email: 'jane@example.com'
        );

        ($this->handler)($command);

        $this->assertCount(1, $this->repository->all());
    }
}
