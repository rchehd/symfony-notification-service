<?php

namespace App\Notification\Application\Handler;

use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Class responsible for handling notification sending logic.
 * This handler iterates over a collection of notification providers and determines
 * the appropriate provider based on the channel specified in the command. If a provider
 * supports the given channel, it attempts to send the notification. In case of failure,
 * it logs errors and retries with the next provider.
 */
#[AsMessageHandler]
class SendNotificationHandler
{
    /**
     * @param iterable<NotifierInterface> $providers
     */
    public function __construct(
        #[TaggedIterator('notification.provider')] private readonly iterable $providers,
        private readonly LoggerInterface $logger,
        private readonly RateLimiterFactory $notificationLimiter,
    ) {
    }

    public function __invoke(NotificationInterface $notification): void
    {
        $isSent = false;

        $recipient  = $notification->getRecipient();
        $channel    = $recipient->getChannel();
        $identifier = $recipient->getIdentifier();

        try {
            // Create a limiter for a recipient by their identifier.
            $limiter = $this->notificationLimiter->create($identifier);
            // If the limit is exceeded, this method will throw an exception.
            $limiter->consume()->ensureAccepted();
        } catch (RateLimitExceededException $exception) {
            // If the limit is exceeded, log it and throw an exception so
            // Messenger can try to process the message later.
            $this->logger->warning(sprintf('Rate limit exceeded for user %s. Retrying in %d seconds.', $identifier, $exception->getRetryAfter()->getTimestamp() - time()));
            throw $exception;
        }

        $this->logger->info(sprintf('Processing notification for %s via %s channel.', $identifier, $channel->value));

        /** @var NotifierInterface $provider */
        foreach ($this->providers as $provider) {
            // Check if provider supports channel.
            if ($provider->supports($channel)) {
                try {
                    // Try to send it.
                    $provider->send($notification);
                    // Set true if it is sent.
                    $isSent = true;
                    $this->logger->info(sprintf('Notification sent successfully via %s.', get_class($provider)));
                    // Break if the current provider works, otherwise - go to next.
                    break;
                } catch (\Throwable $exception) {
                    $this->logger->error(sprintf(
                        'Failed to send notification via %s. Error: %s',
                        get_class($provider),
                        $exception->getMessage()
                    ));
                    // Continue the circle to try another provider.
                    continue;
                }
            }
        }

        // When all providers failed - return the exception.
        if (!$isSent) {
            throw new \Exception(sprintf('All providers failed for channel "%s" for user "%s".', $channel->value, $identifier));
        }
    }
}
