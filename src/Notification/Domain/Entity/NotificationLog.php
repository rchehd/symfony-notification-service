<?php

namespace App\Notification\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Notification\Infrastructure\Repository\NotificationLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationLogRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    paginationItemsPerPage: 30
)]
class NotificationLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $recipientIdentifier;

    #[ORM\Column(length: 50)]
    private string $channel;

    #[ORM\Column(length: 255)]
    private string $providerClass;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $sentAt;

    public function __construct(
        string $recipientIdentifier,
        string $channel,
        string $providerClass,
    ) {
        $this->recipientIdentifier = $recipientIdentifier;
        $this->channel             = $channel;
        $this->providerClass       = $providerClass;
        $this->sentAt              = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipientIdentifier(): string
    {
        return $this->recipientIdentifier;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getProviderClass(): string
    {
        return $this->providerClass;
    }

    public function getSentAt(): \DateTimeImmutable
    {
        return $this->sentAt;
    }
}
