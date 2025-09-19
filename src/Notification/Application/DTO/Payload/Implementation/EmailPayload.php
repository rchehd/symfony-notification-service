<?php

namespace App\Notification\Application\DTO\Payload\Implementation;

use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents the payload structure for an email notification.
 * Implements the NotificationPayloadInterface to ensure compatibility with notification handling logic.
 *
 * Properties:
 * - `subject`: The email's subject line. This field is required.
 * - `textBody`: The main content of the email in plain text format. This field is required.
 * - `htmlBody`: The optional content of the email in HTML format. Defaults to null if not provided.
 */
class EmailPayload implements NotificationPayloadInterface
{
    #[Assert\NotBlank]
    public string $subject;

    #[Assert\NotBlank]
    public string $textBody;

    public ?string $htmlBody = null;
}
