<?php

namespace App\Tests\Unit\Notification\Application\Factory;

use App\Notification\Application\DTO\RecipientDTO;
use App\Notification\Application\Factory\RecipientFactory;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\Implementation\EmailRecipient;
use App\Notification\Domain\ValueObject\Implementation\LogRecipient;
use App\Notification\Domain\ValueObject\Implementation\SmsRecipient;
use App\Notification\Domain\ValueObject\RecipientInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit test class for the RecipientFactory functionality.
 *
 * Verifies the correct creation of recipient objects for a given notification channel
 * and tests handling of invalid scenarios, such as missing required data.
 */
#[CoversClass(RecipientFactory::class)]
class RecipientFactoryTest extends TestCase
{
    private RecipientFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RecipientFactory();
    }

    /**
     * This method provides data for testing successful scenarios.
     *
     * @return iterable<string, array{0: NotificationChannel, 1: string, 2: string, 3: class-string<RecipientInterface>}>
     */
    public static function happyPathProvider(): iterable
    {
        yield 'EMAIL channel' => [NotificationChannel::EMAIL, 'email', 'test@example.com', EmailRecipient::class];
        yield 'SMS channel'   => [NotificationChannel::SMS, 'phoneNumber', '+123456789', SmsRecipient::class];
        yield 'LOG channel'   => [NotificationChannel::LOG, 'username', 'test_user', LogRecipient::class];
    }

    #[DataProvider('happyPathProvider')]
    public function testCreatesRecipientSuccessfully(
        NotificationChannel $channel,
        string $dtoProperty,
        string $identifier,
        string $expectedClass,
    ): void {
        // Arrange
        $dto                 = new RecipientDTO();
        $dto->{$dtoProperty} = $identifier;

        // Act
        $recipient = $this->factory->create($dto, $channel);

        // Assert
        $this->assertSame($identifier, $recipient->getIdentifier());
    }

    /**
     * This method provides data for testing scripts with errors.
     *
     * @return iterable<string, array{0: NotificationChannel, 1: string}>
     */
    public static function exceptionPathProvider(): iterable
    {
        yield 'EMAIL channel missing email' => [NotificationChannel::EMAIL, 'Field "email" is required for the "email" channel.'];
        yield 'SMS channel missing phone' => [NotificationChannel::SMS, 'Field "phoneNumber" is required for the "sms" channel.'];
    }

    #[DataProvider('happyPathProvider')]
    public function testThrowsExceptionWhenIdentifierIsMissing(NotificationChannel $channel, string $expectedMessage): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        // Arrange
        $dto = new RecipientDTO();

        // Act
        $this->factory->create($dto, $channel);
    }

}
