<?php

namespace AppBundle\Entity;

use AppBundle\Service\DownloadableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Справка
 * @ORM\Entity()
 */
class Certificate extends BaseEntity implements DownloadableInterface
{
    /**
     * Город следования
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * Дата начала действия
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFrom;

    /**
     * Дата окончания действия
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateTo;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="certificates")
     */
    private $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="CertificateType")
     */
    private $type;

    /**
     * Документ
     * @ORM\ManyToOne(targetEntity="Document")
     */
    private $document;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix()
    {
        return 'contract';
    }

    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->dateFrom = $createdAt;
        return parent::setCreatedAt($createdAt);
    }

    public function __toString()
    {
        if ($this->type instanceof CertificateType) {
            return (string)$this->type->getName();
        }

        return '';
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Certificate
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
     * Set number
     *
     * @param string $number
     *
     * @return Certificate
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return Certificate
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        if ($this->getType()->getSyncId() == CertificateType::TRAVEL || $this->getType()->getSyncId() == CertificateType::REGISTRATION) {
            return new \DateTime();
        }

        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return Certificate
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        if ($this->getType()->getSyncId() == CertificateType::TRAVEL || $this->getType()->getSyncId() == CertificateType::REGISTRATION) {
            return new \DateTime(date('Y-m-d', strtotime('+ 1 year', time())));
        }

        return $this->dateTo;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Certificate
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
     * @param \AppBundle\Entity\CertificateType $type
     *
     * @return Certificate
     */
    public function setType(CertificateType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\CertificateType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set document
     *
     * @param \AppBundle\Entity\Document $document
     *
     * @return Certificate
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \AppBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
