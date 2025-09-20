<?php
namespace App\Tests\Unit\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\Implementation\EmailRecipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmailRecipient::class)]
class EmailRecipientTest extends TestCase
{
    public function testThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address provided.');

        new EmailRecipient('this-is-not-an-email');
    }

    public function testCreatesSuccessfullyWithValidEmail(): void
    {
        $email     = 'test@example.com';
        $recipient = new EmailRecipient($email);

        $this->assertSame($email, $recipient->getIdentifier());
    }
}
