<?php

declare(strict_types=1);

namespace Modules\User\Domain\Entity;

use Modules\Shared\Domain\ValueObject\Uuid;

final readonly class UserId extends Uuid {}
