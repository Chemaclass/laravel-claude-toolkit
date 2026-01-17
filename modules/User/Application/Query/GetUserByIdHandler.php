<?php

declare(strict_types=1);

namespace Modules\User\Application\Query;

use Modules\User\Domain\Entity\User;
use Modules\User\Domain\Entity\UserId;
use Modules\User\Domain\Exception\UserNotFoundException;
use Modules\User\Domain\Repository\UserRepository;

final readonly class GetUserByIdHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function __invoke(GetUserById $query): User
    {
        $userId = UserId::fromString($query->id);
        $user = $this->userRepository->findById($userId);

        if (! $user instanceof User) {
            throw UserNotFoundException::withId($userId);
        }

        return $user;
    }
}
