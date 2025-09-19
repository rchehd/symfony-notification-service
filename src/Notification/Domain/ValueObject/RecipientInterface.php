<?php

namespace App\Notification\Domain\ValueObject;

use App\Notification\Domain\Enum\NotificationChannel;

/**
 * Defines a contract for a recipient that can receive notifications.
 */
interface RecipientInterface
{
    /**
     * Retrieves the notification channel instance.
     *
     * @return NotificationChannel the notification channel instance
     */
    public function getChannel(): NotificationChannel;

    /**
     * Retrieves the unique identifier.
     *
     * @return string the unique identifier
     */
    public function getIdentifier(): string;
}
