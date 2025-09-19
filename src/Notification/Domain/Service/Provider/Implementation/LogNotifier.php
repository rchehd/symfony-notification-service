<?php

namespace App\Notification\Domain\Service\Provider\Implementation;

use App\Notification\Application\Notification\Implementation\LogNotification;
use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LogNotifier
 *
 * Provides implementation for sending notifications and determining supported channels using a logging mechanism.
 */
class LogNotifier implements NotifierInterface
{
    /**
     *  Construct the LogNotifier object.
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function send(NotificationInterface $notification): void
    {
        if (!$notification instanceof LogNotification) {
            return;
        }

        $recipient = $notification->getRecipient();
        /** @var \App\Notification\Domain\ValueObject\Implementation\LogContent $content */
        $content = $notification->getContent();

        $this->logger->info(sprintf(
            'Message sent via LOG channel. User: %s, Channel: %s, Message: "%s"',
            $recipient->getIdentifier(),
            $recipient->getChannel()->value,
            $content->logMessage
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::LOG === $channel;
    }
}
