<?php

namespace App\Notification\Infrastructure\Serializer;

use App\Notification\Application\DTO\ChannelRequest;
use App\Notification\Domain\Enum\NotificationChannel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Handles the denormalization of data into ChannelRequest objects.
 * Implements DenormalizerInterface to provide custom denormalization logic
 * based on the NotificationChannel and its associated payload.
 */
class ChannelRequestNormalizer implements DenormalizerInterface
{
    /**
     * Constructor method.
     *
     * @param DenormalizerInterface&NormalizerInterface $normalizer The normalizer and denormalizer interface implementation, autowired with the service 'serializer.normalizer.object'.
     */
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly DenormalizerInterface&NormalizerInterface $normalizer,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ChannelRequest
    {
        $channelRequest = new ChannelRequest();

        if (isset($data['channel'])) {
            $channelRequest->channel = NotificationChannel::from($data['channel']);
        }

        if (isset($data['payload'], $channelRequest->channel)) {
            $payloadClass            = $channelRequest->channel->getPayloadClass();
            $channelRequest->payload = $this->normalizer->denormalize($data['payload'], $payloadClass, $format, $context);
        }

        return $channelRequest;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            ChannelRequest::class => true,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return is_a($type, ChannelRequest::class, true);
    }
}
