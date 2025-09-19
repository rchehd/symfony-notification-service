<?php

namespace App\Notification\Application\DTO\Payload\Implementation;

use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a payload for a logging notification.
 *
 * This class encapsulates the message to be logged, ensuring the message is not blank.
 */
class LogPayload implements NotificationPayloadInterface
{
    #[Assert\NotBlank]
    public string $logMessage;
}
