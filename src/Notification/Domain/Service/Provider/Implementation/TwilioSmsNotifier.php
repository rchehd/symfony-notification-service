<?php

namespace App\Notification\Domain\Service\Provider\Implementation;

use App\Notification\Application\Notification\Implementation\SMSNotification;
use App\Notification\Application\Notification\NotificationInterface;
use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Handles sending SMS notifications via the Twilio API.
 *
 * Implements the NotifierInterface to provide support for sending messages
 * through the SMS channel using predefined Twilio account credentials.
 */
class TwilioSmsNotifier implements NotifierInterface
{
    /**
     * Constructs a new instance of the class, initializing essential properties for interacting with the Twilio API and logging.
     *
     * @param string              $accountSid       The Twilio account SID used for authentication
     * @param string              $authToken        The Twilio authentication token
     * @param string              $messageServiceId Identifier for the Twilio messaging service to be used
     * @param HttpClientInterface $client           The HTTP client used to make API requests
     * @param LoggerInterface     $logger           The logger used for tracking application events and errors
     */
    public function __construct(
        protected string $accountSid,
        protected string $authToken,
        protected string $messageServiceId,
        protected HttpClientInterface $client,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * Sends a notification using the Twilio API if the provided notification is of type SMSNotification.
     *
     * @param NotificationInterface $notification the notification object to be sent
     *
     * @throws \RuntimeException if there is an error during the API call or while processing the response
     */
    public function send(NotificationInterface $notification): void
    {
        if (!$notification instanceof SMSNotification) {
            return;
        }

        $recipient = $notification->getRecipient();
        /** @var \App\Notification\Domain\ValueObject\Implementation\SmsContent $content */
        $content   = $notification->getContent();

        $url = sprintf('https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json', $this->accountSid);

        try {
            $response = $this->client->request('POST', $url, [
                'auth_basic' => [$this->accountSid, $this->authToken],
                'body'       => [
                    'To'                  => $recipient->getIdentifier(),
                    'MessagingServiceSid' => $this->messageServiceId,
                    'Body'                => $content->message,
                ],
            ]);

            if ($response->getStatusCode() >= 300) {
                throw new \RuntimeException(sprintf('Twilio API error: %s', $response->getContent(false)));
            }

            $this->logger->info(sprintf(
                'Message sent via SMS channel via Twilio. User: %s, Channel: %s, Message: "%s"',
                $recipient->getIdentifier(),
                $recipient->getChannel()->value,
                $content->message
            ));
        } catch (\Throwable $exception) {
            throw new \RuntimeException(sprintf('Failed to send SMS via Twilio: %s', $exception->getMessage()), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SMS === $channel;
    }
}
