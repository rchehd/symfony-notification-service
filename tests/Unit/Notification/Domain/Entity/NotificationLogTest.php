<?php

namespace App\Tests\Unit\Notification\Domain\Entity;

use App\Notification\Domain\Entity\NotificationLog;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the NotificationLog entity.
 *
 * Ensures that the entity can be created properly and that its getters
 * return the expected values after initialization.
 */
#[CoversClass(NotificationLog::class)]
class NotificationLogTest extends TestCase
{

    /**
     * Tests creation of the NotificationLog entity and validates the correctness of its getters.
     */
    public function testEntityCanBeCreatedAndGettersWork(): void
    {
        // Arrange
        $identifier = 'test@example.com';
        $channel    = 'email';
        $provider   = 'App\TestProvider';

        // Act
        $log = new NotificationLog($identifier, $channel, $provider);

        // Assert
        $this->assertNull($log->getId());

        $this->assertSame($identifier, $log->getRecipientIdentifier());
        $this->assertSame($channel, $log->getChannel());
        $this->assertSame($provider, $log->getProviderClass());
    }
}
