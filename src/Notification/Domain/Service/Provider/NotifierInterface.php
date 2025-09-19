<?php

namespace App\Notification\Domain\Service\Provider;

use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Enum\NotificationChannel;

/**
 * Defines the contract for a notifier service capable of sending notifications and verifying channel support.
 */
interface NotifierInterface
{
    /**
     * Sends a notification based on the provided command.
     *
     * @param NotificationInterface $notification the notification containing the necessary data to send the notification
     */
    public function send(NotificationInterface $notification): void;

    /**
     * Determines if the specified channel is supported.
     *
     * @param NotificationChannel $channel the name of the channel to check for support
     *
     * @return bool true if the channel is supported, false otherwise
     */
    public function supports(NotificationChannel $channel): bool;
}
