<?php

namespace App\Notification\Application\Factory;

use App\Notification\Application\DTO\RecipientDTO;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\ValueObject\Implementation\EmailRecipient;
use App\Notification\Domain\ValueObject\Implementation\LogRecipient;
use App\Notification\Domain\ValueObject\Implementation\SmsRecipient;
use App\Notification\Domain\ValueObject\RecipientInterface;

/**
 * Factory class responsible for creating instances of RecipientInterface based on the specified notification channel.
 */
class RecipientFactory
{
    /**
     * Creates a recipient instance based on the specified notification channel and identifier.
     *
     * @param RecipientDTO        $recipientDTO the recipient DTO
     * @param NotificationChannel $channel      the notification channel for which a recipient should be created
     *
     * @return RecipientInterface the created recipient instance
     */
    public function create(RecipientDTO $recipientDTO, NotificationChannel $channel): RecipientInterface
    {
        return match ($channel) {
            NotificationChannel::EMAIL => new EmailRecipient($recipientDTO->email),
            NotificationChannel::SMS   => new SmsRecipient($recipientDTO->phoneNumber),
            NotificationChannel::LOG   => new LogRecipient($recipientDTO->username),
        };
    }
}
