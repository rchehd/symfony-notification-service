<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Enum\NotificationChannel;
use App\Notification\Domain\Service\Provider\NotifierInterface;

/**
 * Represents a chain of notification providers.
 */
class ProviderChain
{
    /**
     * Construct ProviderChain object.
     *
     * @param iterable<NotifierInterface>                                                      $providers      the providers
     * @param array<string, array<string, array{enabled: bool, priority: int, class: string}>> $providerConfig the provider configs
     */
    public function __construct(
        protected iterable $providers,
        protected array $providerConfig,
    ) {
    }

    /**
     * Get the provider for the channel.
     *
     * @return iterable<NotifierInterface>
     */
    public function getProvider(NotificationChannel $channel): iterable
    {
        $channelName           = $channel->value;
        $activeProvidersConfig = [];

        // Get configuration of the channel.
        if (!isset($this->providerConfig[$channelName])) {
            return [];
        }

        // Filter channels by enabled.
        foreach ($this->providerConfig[$channelName] as $config) {
            if ($config['enabled']) {
                $activeProvidersConfig[] = $config;
            }
        }

        // Sort by priority.
        usort($activeProvidersConfig, static fn ($a, $b) => $b['priority'] <=> $a['priority']);

        // Return services.
        foreach ($activeProvidersConfig as $config) {
            foreach ($this->providers as $providerService) {
                if ($providerService instanceof $config['class'] && $providerService->supports($channel)) {
                    yield $providerService;
                    break;
                }
            }
        }
    }
}
