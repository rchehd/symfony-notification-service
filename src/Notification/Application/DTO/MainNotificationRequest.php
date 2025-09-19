<?php

namespace App\Notification\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a request for sending a notification.
 *
 * This class is used to encapsulate the data required for sending a notification,
 * including the user identifier, message content, and the preferred communication channels.
 *
 * - `$userId`: The identifier of the user to whom the notification is addressed.
 * - `$message`: The content of the notification message to be sent.
 * - `$channels`: The array of notification channels through which the message will be delivered,
 *   limited to the supported values: 'sms', 'email', or 'push'.
 */
class MainNotificationRequest
{
    #[Assert\Valid]
    #[Assert\NotNull]
    public RecipientDTO $recipient;

    /**
     * @var ChannelRequest[]
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    public array $notifications;
}
