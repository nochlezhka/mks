<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Полученная услуга
 */
#[ORM\Entity]
class Service extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    /**
     * Сумма денег
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $amount = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'services')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: ServiceType::class)]
    private ?ServiceType $type = null;

    public function __toString(): string
    {
        $type = $this->getType();

        if ($type instanceof ServiceType) {
            return $type->getName() ?? '';
        }

        return '';
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?ServiceType
    {
        return $this->type;
    }

    public function setType(ServiceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
