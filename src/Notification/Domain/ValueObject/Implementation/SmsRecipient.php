<?php

namespace App\Notification\Domain\ValueObject\Implementation;

use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\RecipientInterface;
use Webmozart\Assert\Assert;

/**
 * Represents a recipient for SMS notifications.
 *
 * Implements the RecipientInterface to define the behavior for an SMS notification recipient.
 */
class SmsRecipient implements RecipientInterface
{
    /**
     *  The phone number.
     */
    private string $phoneNumber;

    public function __construct(string $phoneNumber)
    {
        Assert::regex($phoneNumber, '/^\+[1-9]\d{1,14}$/', 'Invalid phone number format. E.g., +11234567890');
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::SMS;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->phoneNumber;
    }
}
