<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Service\DownloadableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Справка
 */
#[ORM\Entity]
class Certificate extends BaseEntity implements DownloadableInterface
{
    /**
     * Город следования
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateFrom = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateTo = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'certificates')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: CertificateType::class)]
    private ?CertificateType $type = null;

    #[ORM\ManyToOne(targetEntity: Document::class)]
    private ?Document $document = null;

    public function __toString(): string
    {
        return $this->type?->getName() ?? '';
    }

    public function getNamePrefix(): string
    {
        return 'contract';
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->dateFrom = $createdAt;

        return parent::setCreatedAt($createdAt);
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeImmutable
    {
        if ($this->getType()->getSyncId() === CertificateType::TRAVEL || $this->getType()->getSyncId() === CertificateType::REGISTRATION) {
            return new \DateTimeImmutable();
        }

        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeImmutable $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function getDateTo(): ?\DateTimeImmutable
    {
        if ($this->getType()->getSyncId() === CertificateType::TRAVEL || $this->getType()->getSyncId() === CertificateType::REGISTRATION) {
            return new \DateTimeImmutable(date('Y-m-d', strtotime('+ 1 year', time())));
        }

        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeImmutable $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?CertificateType
    {
        return $this->type;
    }

    public function setType(?CertificateType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }
}
