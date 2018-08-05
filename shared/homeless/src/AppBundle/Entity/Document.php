<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
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
    private $address;

    /**
     * Город
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * Дата
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * Серия
     * @ORM\Column(type="string", nullable=true)
     */
    private $numberPrefix;

    /**
     * Регистрация
     * @ORM\Column(type="integer", nullable=true)
     */
    private $registration;

    /**
     * Кем и когда выдан
     * @ORM\Column(type="string", nullable=true)
     */
    private $issued;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documents")
     */
    private $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="DocumentType")
     */
    private $type;

    public function __toString()
    {
        $string = '';

        $type = $this->getType();

        if ($type instanceof DocumentType) {
            $string .= $type->getName();
        }

        if ($this->numberPrefix) {
            $string .= ' ' . $this->numberPrefix;
        }

        if ($this->number) {
            $string .= ' ' . $this->number;
        }

        if ($this->issued) {
            $string .= ' выдан ' . $this->issued;
        }

        if ($this->date instanceof \DateTime) {
            $string .= ' ' . $this->date->format('d.m.Y');
        }

        return $string;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Document
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
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
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Document
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Document
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
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
    public function setNumberPrefix($numberPrefix)
    {
        $this->numberPrefix = $numberPrefix;

        return $this;
    }

    /**
     * Get numberPrefix
     *
     * @return string
     */
    public function getNumberPrefix()
    {
        return $this->numberPrefix;
    }

    /**
     * Set registration
     *
     * @param integer $registration
     *
     * @return Document
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return integer
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Set issued
     *
     * @param string $issued
     *
     * @return Document
     */
    public function setIssued($issued)
    {
        $this->issued = $issued;

        return $this;
    }

    /**
     * Get issued
     *
     * @return string
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Document
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\DocumentType $type
     *
     * @return Document
     */
    public function setType(DocumentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\DocumentType
     */
    public function getType()
    {
        return $this->type;
    }
}
