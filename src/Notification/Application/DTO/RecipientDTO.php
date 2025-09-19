<?php

namespace App\Notification\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RecipientDTO
{
    #[Assert\Email]
    #[Assert\NotNull]
    public ?string $email;

    #[Assert\Regex('/^\+[1-9]\d{1,14}$/')]
    #[Assert\NotNull]
    public ?string $phoneNumber;

    #[Assert\NotNull]
    public ?string $username;
}
