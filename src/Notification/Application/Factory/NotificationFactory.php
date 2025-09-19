<?php

namespace App\Notification\Application\Factory;

use App\Notification\Application\DTO\MainNotificationRequest;
use App\Notification\Application\DTO\Payload\Implementation\EmailPayload;
use App\Notification\Application\DTO\Payload\Implementation\LogPayload;
use App\Notification\Application\DTO\Payload\Implementation\SmsPayload;
use App\Notification\Application\Notification\Implementation\EmailNotification;
use App\Notification\Application\Notification\Implementation\LogNotification;
use App\Notification\Application\Notification\Implementation\SMSNotification;
use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\ValueObject\Implementation\EmailContent;
use App\Notification\Domain\ValueObject\Implementation\LogContent;
use App\Notification\Domain\ValueObject\Implementation\SmsContent;

/**
 * A factory class responsible for creating notification objects
 * based on the provided channel and payload specifications.
 */
class NotificationFactory
{
    /**
     * Constructor method for initializing the RecipientFactory dependency.
     *
     * @param RecipientFactory    $recipientFactory an instance of RecipientFactory to manage recipient-related functionality
     * @param array<string, bool> $enabledChannels  list of enabled channels
     */
    public function __construct(
        private readonly RecipientFactory $recipientFactory,
        private readonly array $enabledChannels,
    ) {
    }

    /**
     * Creates an array of notifications based on the provided request DTO.
     *
     * This method processes the notifications specified in the MainNotificationRequest
     * and generates corresponding notification objects for the enabled channels.
     *
     * @param MainNotificationRequest $requestDTO data Transfer Object containing the details
     *                                            for creating notifications, including recipient,
     *                                            channels, and payloads
     *
     * @throws \InvalidArgumentException if an unsupported payload type is provided
     *
     * @return NotificationInterface[] an array of notification objects generated based on the request
     */
    public function createFromRequest(MainNotificationRequest $requestDTO): array
    {
        $notifications = [];

        foreach ($requestDTO->notifications as $channelRequest) {
            $channelName = $channelRequest->channel->value;

            // Check if a channel is enabled.
            if (!isset($this->enabledChannels[$channelName]) || !$this->enabledChannels[$channelName]) {
                continue;
            }

            // Create a notification.
            $recipient = $this->recipientFactory->create($requestDTO->recipient, $channelRequest->channel);

            $notification = match ($channelRequest->payload::class) {
                EmailPayload::class => new EmailNotification(
                    $recipient,
                    new EmailContent(
                        $channelRequest->payload->subject,
                        $channelRequest->payload->textBody,
                        $channelRequest->payload->htmlBody
                    )
                ),
                LogPayload::class => new LogNotification(
                    $recipient,
                    new LogContent(
                        $channelRequest->payload->logMessage
                    )
                ),
                SmsPayload::class => new SMSNotification(
                    $recipient,
                    new SmsContent(
                        $channelRequest->payload->message
                    )
                ),
                default => throw new \InvalidArgumentException('Unsupported payload type provided.'),
            };

            $notifications[] = $notification;
        }

        return $notifications;
    }
}
