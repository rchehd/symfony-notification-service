<?php

namespace App\Notification\Domain\ValueObject\Implementation;

use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\RecipientInterface;
use Webmozart\Assert\Assert;

/**
 * Represents a recipient for email notifications.
 * Implements the RecipientInterface to define notification behavior.
 *
 * The EmailRecipient class is responsible for handling email-based notification channels
 * and providing the associated email identifier.
 */
class EmailRecipient implements RecipientInterface
{
    /**
     * The email address.
     */
    private string $email;

    public function __construct(string $email)
    {
        Assert::email($email, 'Invalid email address provided.');
        $this->email = $email;
    }

    /**
     * {@inheritDoc}
     */
    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::EMAIL;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->email;
    }
}
