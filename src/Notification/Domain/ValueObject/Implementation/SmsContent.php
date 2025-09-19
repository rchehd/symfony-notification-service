<?php

namespace App\Notification\Domain\ValueObject\Implementation;

use App\Notification\Domain\ValueObject\MessageContentInterface;

/**
 * Represents the content of an SMS message.
 *
 * Implements the MessageContentInterface to provide compatibility
 * as a message content implementation.
 *
 * This class holds the message text as a readonly property, making
 * it immutable once an instance is created.
 */
class SmsContent implements MessageContentInterface
{
    /**
     * Constructor method to initialize the object with a message.
     *
     * @param string $message the message to be assigned as a readonly property
     *
     * @return void
     */
    public function __construct(
        public string $message,
    ) {
    }
}
