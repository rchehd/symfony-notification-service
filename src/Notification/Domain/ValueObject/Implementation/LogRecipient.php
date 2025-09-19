<?php

namespace App\Notification\Domain\ValueObject\Implementation;

use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\RecipientInterface;

/**
 * Represents a recipient for SMS notifications.
 *
 * Implements the RecipientInterface to define the behavior for an SMS notification recipient.
 */
class LogRecipient implements RecipientInterface
{
    /**
     * Constructor method for initializing the object with a user name.
     *
     * @param string $userName the name of the user
     *
     * @return void
     */
    public function __construct(protected string $userName)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::LOG;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->userName;
    }
}
