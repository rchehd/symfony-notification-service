<?php

namespace App\Notification\Application\Notification;

use App\Notification\Domain\ValueObject\MessageContentInterface;
use App\Notification\Domain\ValueObject\RecipientInterface;

/**
 * Abstract class representing the base for all notification types.
 * Implements the NotificationInterface to ensure a consistent API
 * across different notification mechanisms.
 */
abstract class NotificationBase implements NotificationInterface
{
    /**
     *  Construct the LogNotification object.
     */
    public function __construct(
        protected RecipientInterface $recipient,
        protected MessageContentInterface $content,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getRecipient(): RecipientInterface
    {
        return $this->recipient;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(): MessageContentInterface
    {
        return $this->content;
    }
}
