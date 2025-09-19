<?php

namespace App\Notification\Application\DTO;

use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;
use App\Notification\Domain\Enum\NotificationChannel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a request with a notification channel and its payload.
 *
 * This class ensures the notification channel is not null and
 * the payload provided adheres to validation rules.
 */
class ChannelRequest
{
    #[Assert\NotNull]
    public NotificationChannel $channel;

    #[Assert\Valid]
    public NotificationPayloadInterface $payload;
}
