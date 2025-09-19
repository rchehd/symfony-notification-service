<?php

namespace App\Notification\Domain\Service\Provider\Implementation;

use App\Notification\Application\Notification\Implementation\SMSNotification;
use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SMS
 *
 * Provides implementation for sending notifications and determining supported channels using SMS provider.
 */
class SMSNotifier implements NotifierInterface
{
    /**
     *  Construct the SMSNotifier object.
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function send(NotificationInterface $notification): void
    {
        if (!$notification instanceof SMSNotification) {
            return;
        }

        $recipient = $notification->getRecipient();
        /** @var \App\Notification\Domain\ValueObject\Implementation\SmsContent $content */
        $content = $notification->getContent();

        $this->logger->info(sprintf(
            'Message sent via SMS channel. User: %s, Channel: %s, Message: "%s"',
            $recipient->getIdentifier(),
            $recipient->getChannel()->value,
            $content->message
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SMS === $channel;
    }
}
