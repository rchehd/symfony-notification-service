<?php

namespace App\Notification\Application\DTO\Payload\Implementation;

use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents the payload for an SMS notification.
 *
 * Implements the NotificationPayloadInterface to ensure compliance with
 * the expected structure for notification payloads.
 *
 * Contains a message property which must not be blank. This is validated
 * using an assertion to ensure the payload contains the necessary content.
 */
class SmsPayload implements NotificationPayloadInterface
{
    #[Assert\NotBlank]
    public string $message;
}
