<?php
namespace App\Tests\Unit\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\Implementation\SmsRecipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SmsRecipient::class)]
class SmsRecipientTest extends TestCase
{
    public function testThrowsExceptionForInvalidPhoneNumberFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number format. E.g., +11234567890');

        new SmsRecipient('this-is-not-an-phone-number');
    }

    public function testCreatesSuccessfullyWithValidPhoneNumberFormat(): void
    {
        $phoneNumber = '+11234567890';
        $recipient   = new SmsRecipient($phoneNumber);

        $this->assertSame($phoneNumber, $recipient->getIdentifier());
    }
}
