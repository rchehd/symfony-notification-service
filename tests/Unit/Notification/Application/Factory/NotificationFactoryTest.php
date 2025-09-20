<?php

namespace App\Tests\Unit\Notification\Application\Factory;

use App\Notification\Application\DTO\ChannelRequest;
use App\Notification\Application\DTO\MainNotificationRequest;
use App\Notification\Application\DTO\Payload\Implementation\EmailPayload;
use App\Notification\Application\DTO\Payload\Implementation\LogPayload;
use App\Notification\Application\DTO\Payload\Implementation\SmsPayload;
use App\Notification\Application\DTO\RecipientDTO;
use App\Notification\Application\Factory\NotificationFactory;
use App\Notification\Application\Factory\RecipientFactory;
use App\Notification\Application\Notification\Implementation\EmailNotification;
use App\Notification\Application\Notification\Implementation\LogNotification;
use App\Notification\Application\Notification\Implementation\SMSNotification;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\RecipientInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the NotificationFactory.
 *
 * This class contains unit tests for the NotificationFactory, ensuring its
 * correctness in creating notifications based on enabled channels and provided
 * request data. It uses mocked dependencies for the RecipientFactory to
 * isolate the notification creation logic. The tests verify that notifications
 * are generated for enabled channels and excluded for disabled ones, and that
 * the generated notifications conform to expected types.
 */
#[CoversClass(NotificationFactory::class)]
class NotificationFactoryTest extends TestCase
{
    private RecipientFactory $recipientFactoryMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->recipientFactoryMock = $this->createMock(RecipientFactory::class);

        $this->recipientFactoryMock
            ->method('create')
            ->willReturn($this->createMock(RecipientInterface::class));
    }

    /**
     * Tests that all notifications are properly created when all notification channels are enabled.
     *
     * This method ensures that when all channels (email, SMS, log) are enabled,
     * the notification factory correctly creates a notification for each channel.
     */
    public function testCreatesAllNotificationsWhenChannelsAreEnabled(): void
    {
        // Arrange
        // All channels are enabled.
        $enabledChannels     = ['email' => true, 'sms' => true, 'log' => true];
        $notificationFactory = new NotificationFactory($this->recipientFactoryMock, $enabledChannels);

        // Create DTO.
        $requestDTO = $this->createMainRequestWithChannels(
            NotificationChannel::EMAIL,
            NotificationChannel::SMS,
            NotificationChannel::LOG,
        );

        // Act.
        $notifications = $notificationFactory->createFromRequest($requestDTO);

        // Assert.
        $this->assertCount(3, $notifications);
        $this->assertInstanceOf(EmailNotification::class, $notifications[0]);
        $this->assertInstanceOf(SMSNotification::class, $notifications[1]);
        $this->assertInstanceOf(LogNotification::class, $notifications[2]);
    }

    /**
     * Tests that the notification factory filters out disabled channels and only creates notifications for enabled channels.
     *
     * The test specifically verifies that when the SMS channel is disabled, only the enabled EMAIL channel produces a notification.
     * It ensures that:
     * - Notification factory respects the configuration of enabled and disabled channels.
     * - The resulting notifications match the expected type and count.
     */
    public function testFiltersDisabledChannels(): void
    {
        // Arrange.
        // SMS channel is disabled.
        $enabledChannels     = ['email' => true, 'sms' => false, 'log' => false];
        $notificationFactory = new NotificationFactory($this->recipientFactoryMock, $enabledChannels);

        // Create DTO.
        $requestDTO = $this->createMainRequestWithChannels(
            NotificationChannel::EMAIL,
            NotificationChannel::SMS,
            NotificationChannel::LOG,
        );

        // Act.
        $notifications = $notificationFactory->createFromRequest($requestDTO);

        // Assert.
        // Check if there is only one notification.
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EmailNotification::class, $notifications[0]);
    }

    /**
     * Creates a MainNotificationRequest using the provided notification channels.
     *
     * This method initializes a MainNotificationRequest, sets its recipient as a
     * new RecipientDTO, and processes the provided NotificationChannel instances.
     * For each channel, it creates a ChannelRequest, assigns the channel to it,
     * and sets the respective payload based on the type of NotificationChannel.
     * The resultant ChannelRequests are added to the notifications property of
     * the MainNotificationRequest.
     *
     * @param NotificationChannel ...$channels Variable list of notification channels
     *
     * @return MainNotificationRequest The constructed MainNotificationRequest object
     */
    private function createMainRequestWithChannels(NotificationChannel ...$channels): MainNotificationRequest
    {
        $mainRequest            = new MainNotificationRequest();
        $mainRequest->recipient = new RecipientDTO();

        $notifications = [];
        foreach ($channels as $channel) {
            $channelRequest = new ChannelRequest();

            $channelRequest->channel = $channel;

            $channelRequest->payload = match ($channel) {
                NotificationChannel::EMAIL => (static function () {
                    $payload           = new EmailPayload();
                    $payload->subject  = 'Test Subject';
                    $payload->textBody = 'Test Body';
                    $payload->htmlBody = '<p>Test Body</p>';

                    return $payload;
                })(),
                NotificationChannel::SMS => (static function () {
                    $payload          = new SmsPayload();
                    $payload->message = 'Test SMS';

                    return $payload;
                })(),
                NotificationChannel::LOG => (static function () {
                    $payload             = new LogPayload();
                    $payload->logMessage = 'Test Log';

                    return $payload;
                })(),
            };

            $notifications[] = $channelRequest;
        }

        $mainRequest->notifications = $notifications;

        return $mainRequest;
    }
}
