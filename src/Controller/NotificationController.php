<?php

namespace App\Controller;

use App\Notification\Application\DTO\MainNotificationRequest;
use App\Notification\Application\Factory\NotificationFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller responsible for handling notification-related API requests.
 */
final class NotificationController extends AbstractController
{
    /**
     *  Construct the NotificationController object.
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    #[Route('/api/notifications', name: 'api_send_notification', methods: ['POST'])]
    public function send(
        #[MapRequestPayload] MainNotificationRequest $requestDTO,
        MessageBusInterface $bus,
        NotificationFactory $factory,
    ): JsonResponse {
        $notificationsToDispatch = $factory->createFromRequest($requestDTO);

        foreach ($notificationsToDispatch as $notification) {
            try {
                $bus->dispatch($notification);
            } catch (ExceptionInterface $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }

        return $this->json(['message' => 'Notifications have been queued.']);
    }
}
