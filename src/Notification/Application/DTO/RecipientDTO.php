<?php

namespace App\Notification\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RecipientDTO
{
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Regex('/^\+[1-9]\d{1,14}$/')]
    public ?string $phoneNumber = null;

    public ?string $username = null;
}
