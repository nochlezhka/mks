<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Документ
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document extends BaseEntity
{
    const REGISTRATION_UNKNOWN = 0;
    const REGISTRATION_YES = 1;
    const REGISTRATION_NO = 2;

    /**
     * Адрес
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $address = null;

    /**
     * Город
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $city = null;

    /**
     * Дата
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $date = null;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $number = null;

    /**
     * Серия
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $numberPrefix = null;

    /**
     * Регистрация
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $registration = null;

    /**
     * Кем и когда выдан
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $issued = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documents")
     */
    private ?Client $client = null;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="DocumentType")
     */
    private ?DocumentType $type = null;

    public function __toString()
    {
        $string = '';

        $type = $this->getType();

        $string .= $type->getName();

        if ($this->numberPrefix) {
            $string .= ' ' . $this->numberPrefix;
        }

        if ($this->number) {
            $string .= ' ' . $this->number;
        }

        if ($this->issued) {
            $string .= ' выдан ' . $this->issued;
        }

        $string .= ' ' . $this->date->format('d.m.Y');

        return $string;
    }

    /**
     * Set address
     *
     * @param string|null $address
     *
     * @return Document
     */
    public function setAddress(?string $address): Document
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Document
     */
    public function setCity(?string $city): Document
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Set date
     *
     * @param DateTime|null $date
     *
     * @return Document
     */
    public function setDate(?DateTime $date): Document
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * Set number
     *
     * @param string|null $number
     *
     * @return Document
     */
    public function setNumber(?string $number): Document
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Set numberPrefix
     *
     * @param string $numberPrefix
     *
     * @return Document
     */
    public function setNumberPrefix(?string $numberPrefix): Document
    {
        $this->numberPrefix = $numberPrefix;

        return $this;
    }

    /**
     * Get numberPrefix
     *
     * @return string
     */
    public function getNumberPrefix(): ?string
    {
        return $this->numberPrefix;
    }

    /**
     * Set registration
     *
     * @param int|null $registration
     *
     * @return Document
     */
    public function setRegistration(?int $registration): Document
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return integer
     */
    public function getRegistration(): ?int
    {
        return $this->registration;
    }

    /**
     * Set issued
     *
     * @param string|null $issued
     *
     * @return Document
     */
    public function setIssued(?string $issued): Document
    {
        $this->issued = $issued;

        return $this;
    }

    /**
     * Get issued
     *
     * @return string
     */
    public function getIssued(): ?string
    {
        return $this->issued;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Document
     */
    public function setClient(Client $client): Document
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Set type
     *
     * @param DocumentType|null $type
     *
     * @return Document
     */
    public function setType(DocumentType $type): Document
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return DocumentType
     */
    public function getType(): ?DocumentType
    {
        return $this->type;
    }
}
