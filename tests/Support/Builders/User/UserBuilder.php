<?php

declare(strict_types=1);

namespace Tests\Support\Builders\User;

use Modules\Shared\Domain\ValueObject\Email;
use Modules\User\Domain\Entity\User;
use Modules\User\Domain\Entity\UserId;

final class UserBuilder
{
    private UserId $id;

    private string $name = 'John Doe';

    private Email $email;

    public function __construct()
    {
        $this->id = UserId::generate();
        $this->email = Email::fromString('john@example.com');
    }

    public static function aUser(): self
    {
        return new self;
    }

    public function withId(UserId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withEmail(Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function build(): User
    {
        return User::create(
            $this->id,
            $this->name,
            $this->email,
        );
    }
}
