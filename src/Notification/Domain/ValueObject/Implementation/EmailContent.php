<?php

namespace App\Notification\Domain\ValueObject\Implementation;

use App\Notification\Domain\ValueObject\MessageContentInterface;

/**
 * Represents the content of an email message.
 *
 * This class encapsulates the subject, text body, and optional HTML body
 * of an email and implements the MessageContentInterface.
 */
class EmailContent implements MessageContentInterface
{
    /**
     * Constructor for initializing the object with subject, text body, and optional HTML body.
     *
     * @param string      $subject  the subject of the content
     * @param string      $textBody the plain text version of the content
     * @param string|null $htmlBody the optional HTML version of the content
     */
    public function __construct(
        public string $subject,
        public string $textBody,
        public ?string $htmlBody = null,
    ) {
    }
}
