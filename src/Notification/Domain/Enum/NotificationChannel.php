<?php

namespace App\Notification\Domain\Enum;

use App\Notification\Application\DTO\Payload\Implementation\EmailPayload;
use App\Notification\Application\DTO\Payload\Implementation\LogPayload;
use App\Notification\Application\DTO\Payload\Implementation\SmsPayload;
use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;

/**
 * NotificationChannel defines a set of communication channels.
 * It represents the medium through which notifications are sent.
 */
enum NotificationChannel: string
{
    case SMS   = 'sms';
    case EMAIL = 'email';
    case LOG   = 'log';

    /**
     * Return DTO class, that is the responsible payload for this channel.
     *
     * @return class-string<NotificationPayloadInterface>
     */
    public function getPayloadClass(): string
    {
        return match ($this) {
            self::SMS   => SmsPayload::class,
            self::EMAIL => EmailPayload::class,
            self::LOG   => LogPayload::class,
        };
    }
}
