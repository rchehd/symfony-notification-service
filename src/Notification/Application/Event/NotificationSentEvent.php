<?php

namespace App\Notification\Application\Event;

/**
 *  Send notification event.
 */
class NotificationSentEvent
{
    /**
     *  Construct NotificationSentEvent object.
     */
    public function __construct(
        public readonly string $recipientIdentifier,
        public readonly string $channel,
        public readonly string $providerClass,
    ) {
    }
}
