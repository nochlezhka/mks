<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Документ
 */
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document extends BaseEntity
{
    public const int REGISTRATION_UNKNOWN = 0;
    public const int REGISTRATION_YES = 1;
    public const int REGISTRATION_NO = 2;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $date = null;

    /**
     * Номер
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $number = null;

    /**
     * Серия
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $numberPrefix = null;

    /**
     * Регистрация
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $registration = null;

    /**
     * Кем и когда выдан
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $issued = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'documents')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: DocumentType::class)]
    private ?DocumentType $type = null;

    public function __toString(): string
    {
        $string = '';

        $type = $this->getType();

        $string .= $type->getName();

        if ($this->numberPrefix) {
            $string .= ' '.$this->numberPrefix;
        }

        if ($this->number) {
            $string .= ' '.$this->number;
        }

        if ($this->issued) {
            $string .= ' выдан '.$this->issued;
        }

        $string .= ' '.$this->date->format('d.m.Y');

        return $string;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): self
    {
        $this->date = $date;

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

    public function getNumberPrefix(): ?string
    {
        return $this->numberPrefix;
    }

    public function setNumberPrefix(?string $numberPrefix): self
    {
        $this->numberPrefix = $numberPrefix;

        return $this;
    }

    public function getRegistration(): ?int
    {
        return $this->registration;
    }

    public function setRegistration(?int $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getIssued(): ?string
    {
        return $this->issued;
    }

    public function setIssued(?string $issued): self
    {
        $this->issued = $issued;

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

    public function getType(): ?DocumentType
    {
        return $this->type;
    }

    public function setType(?DocumentType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
