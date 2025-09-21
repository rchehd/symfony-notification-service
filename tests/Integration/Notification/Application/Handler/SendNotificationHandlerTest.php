<?php

namespace App\Tests\Integration\Notification\Application\Handler;

use App\Notification\Application\Event\NotificationSentEvent;
use App\Notification\Application\Handler\SendNotificationHandler;
use App\Notification\Application\Notification\Implementation\SMSNotification;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use App\Notification\Domain\Service\ProviderChain;
use App\Notification\Domain\ValueObject\Implementation\SmsContent;
use App\Notification\Domain\ValueObject\Implementation\SmsRecipient;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Class SendNotificationHandlerTest.
 *
 * Contains tests for validating the behavior and logic of the `SendNotificationHandler` class,
 * particularly in scenarios where notifications need to be sent through a sequence of providers
 * with failover mechanisms in place. This test suite focuses on ensuring the correct functioning
 * of failover mechanisms, logging, event dispatching, and overall notification handling.
 */
class SendNotificationHandlerTest extends TestCase
{

    /**
     * Tests that the failover logic for sending notifications works correctly.
     *
     * This method validates that when a notification is sent through a sequence of providers:
     * - If the first provider fails, the failover mechanism ensures the next provider in line is used.
     * - The failing provider's `send` method is invoked once.
     * - The succeeding provider's `send` method is invoked once.
     * - Errors from failing providers are logged correctly.
     * - An event is dispatched upon the successful delivery of the notification.
     *
     * The logic simulates the following:
     * - Mocks of `NotifierInterface` providers with pre-set behaviors (success and failure).
     * - Supporting dependencies for logging, event dispatching, and rate limiting.
     * - A test SMS notification to verify the handling process.
     *
     * Assertions ensure:
     * - Each provider's `send` method is triggered the expected number of times.
     * - The error from the failing provider is logged.
     * - The successful notification dispatch event is captured.
     */
    public function testFailoverLogicWorksCorrectly(): void
    {
        // ARRANGE

        // 1. We create imitations (Moki) of our providers.
        $failingProvider    = $this->createMock(NotifierInterface::class);
        $successfulProvider = $this->createMock(NotifierInterface::class);

        // 2. We adjust their behavior
        // The first provider always supports SMS and always "falls".
        $failingProvider->method('supports')->willReturn(true);
        $failingProvider->method('send')->willThrowException(new \RuntimeException('Provider failed!'));

        // The second provider also supports SMS and always works successfully.
        $successfulProvider->method('supports')->willReturn(true);
        // The Send method does nothing, just successfully work out.
        $providerChainMock = $this->createMock(ProviderChain::class);
        $providerChainMock->method('getProvider')
            ->with(NotificationChannel::SMS)
            ->willReturn([$failingProvider, $successfulProvider]);


        // 3. We create mooks for other addictions.
        $logger          = $this->createMock(LoggerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        // Set up the mooks for Rate Limiter
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturnSelf();

        $limiterConfig  = ['id' => 'test_limiter', 'policy' => 'no_limit'];
        $limiterStorage = new InMemoryStorage();
        $limiterFactory = new RateLimiterFactory($limiterConfig, $limiterStorage);

        // 4. We create our handler by handing it to our mooks.
        // We pass the providers in the order we want to check them.
        $handler = new SendNotificationHandler(
            $providerChainMock,
            $logger,
            $limiterFactory,
            $eventDispatcher
        );

        // 5. We create test notifications.
        $recipient    = new SmsRecipient('+123456');
        $content      = new SmsContent('Test message');
        $notification = new SMSNotification($recipient, $content);

        // ASSERT

        // We expect the Send () method will be caused once in a "broken"
        // provider.
        $failingProvider->expects($this->once())->method('send');

        // And once in a successful provider (because Failover will work).
        $successfulProvider->expects($this->once())->method('send');

        // We expect an error to be recorded in log once.
        $logger->expects($this->once())->method('error');

        // We expect an event about successful shipment will be sent once.
        $eventDispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(NotificationSentEvent::class));

        // ACT
        $handler->__invoke($notification);
    }
}
