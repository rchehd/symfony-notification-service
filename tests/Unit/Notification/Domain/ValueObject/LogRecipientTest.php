<?php
namespace App\Tests\Unit\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\Implementation\LogRecipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogRecipient::class)]
class LogRecipientTest extends TestCase
{

    public function testCreatesSuccessfullyWithValidUserName(): void
    {
        $username  = 'test';
        $recipient = new LogRecipient($username);

        $this->assertSame($username, $recipient->getIdentifier());
    }
}
