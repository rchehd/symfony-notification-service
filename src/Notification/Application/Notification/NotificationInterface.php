<?php

namespace App\Notification\Application\Notification;

use App\Notification\Domain\ValueObject\MessageContentInterface;
use App\Notification\Domain\ValueObject\RecipientInterface;

/**
 * Interface NotificationInterface
 *
 * Represents a contract for notification-related data retrieval.
 */
interface NotificationInterface
{
    /**
     * Retrieves the recipient associated with the current context.
     *
     * @return RecipientInterface the recipient object
     */
    public function getRecipient(): RecipientInterface;

    /**
     * Retrieves the content associated with the current message context.
     *
     * @return MessageContentInterface the message content object
     */
    public function getContent(): MessageContentInterface;
}
