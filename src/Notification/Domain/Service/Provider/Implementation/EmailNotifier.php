<?php

namespace App\Notification\Domain\Service\Provider\Implementation;

use App\Notification\Application\Notification\Implementation\EmailNotification;
use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * The SymfonyMailerNotifier class is responsible for sending email notifications
 * through Symfony Mailer and implementing the NotifierInterface.
 */
class EmailNotifier implements NotifierInterface
{
    /**
     * Construct the SymfonyMailerNotifier object.
     */
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function send(NotificationInterface $notification): void
    {
        if (!$notification instanceof EmailNotification) {
            return;
        }

        $recipient = $notification->getRecipient();
        /** @var \App\Notification\Domain\ValueObject\Implementation\EmailContent $content */
        $content = $notification->getContent();

        $this->logger->info(sprintf('Preparing to send email to %s', $recipient->getIdentifier()));

        $email = (new Email())
            ->from('noreply@example.com')
            ->to($recipient->getIdentifier())
            ->subject($content->subject)
            ->text($content->textBody);

        if ($content->htmlBody) {
            $email->html($content->htmlBody);
        }

        $this->mailer->send($email);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::EMAIL === $channel;
    }
}
