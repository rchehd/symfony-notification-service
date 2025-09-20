<?php

namespace App\Notification\Infrastructure\EventSubscriber;

use App\Notification\Application\Event\NotificationSentEvent;
use App\Notification\Domain\Entity\NotificationLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The NotificationLogSubscriber class listens to the NotificationSentEvent
 * and handles the creation and persistence of a log entry for the notification.
 */
class NotificationLogSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor initializes the service with an EntityManagerInterface instance.
     *
     * @param EntityManagerInterface $entityManager the entity manager instance
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            NotificationSentEvent::class => 'onNotificationSent',
        ];
    }

    /**
     * Handles the notification sent event by creating a new log entry
     * and persisting it in the database.
     *
     * @param NotificationSentEvent $event the event containing notification details
     */
    public function onNotificationSent(NotificationSentEvent $event): void
    {
        // Create a Notification Log.
        $logEntry = new NotificationLog(
            $event->recipientIdentifier,
            $event->channel,
            $event->providerClass
        );

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }
}
