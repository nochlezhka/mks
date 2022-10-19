<?php

namespace AppBundle\Entity;

use AppBundle\Service\DownloadableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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
    private ?string $city = null;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $number = null;

    /**
     * Дата начала действия
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateFrom = null;

    /**
     * Дата окончания действия
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime|null
     */
    private ?DateTime $dateTo = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="certificates")
     */
    private ?Client $client = null;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="CertificateType")
     */
    private ?CertificateType $type = null;

    /**
     * Документ
     * @ORM\ManyToOne(targetEntity="Document")
     */
    private ?Document $document = null;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix(): string
    {
        return 'contract';
    }

    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->dateFrom = $createdAt;
        return parent::setCreatedAt($createdAt);
    }

    public function __toString()
    {
        return $this->type->getName();
    }

    /**
     * Set city
     *
     * @param string|null $city
     *
     * @return Certificate
     */
    public function setCity(?string $city): Certificate
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
     * Set number
     *
     * @param string|null $number
     *
     * @return Certificate
     */
    public function setNumber(?string $number): Certificate
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
     * Set dateFrom
     *
     * @param DateTime|null $dateFrom
     *
     * @return Certificate
     */
    public function setDateFrom(?DateTime $dateFrom): Certificate
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return DateTime
     */
    public function getDateFrom(): ?DateTime
    {
        if ($this->getType()->getSyncId() == CertificateType::TRAVEL || $this->getType()->getSyncId() == CertificateType::REGISTRATION) {
            return new DateTime();
        }

        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param DateTime|null $dateTo
     *
     * @return Certificate
     */
    public function setDateTo(?DateTime $dateTo): Certificate
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return DateTime
     * @throws Exception
     */
    public function getDateTo(): ?DateTime
    {
        if ($this->getType()->getSyncId() == CertificateType::TRAVEL || $this->getType()->getSyncId() == CertificateType::REGISTRATION) {
            return new DateTime(date('Y-m-d', strtotime('+ 1 year', time())));
        }

        return $this->dateTo;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Certificate
     */
    public function setClient(Client $client): Certificate
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
     * @param CertificateType|null $type
     *
     * @return Certificate
     */
    public function setType(CertificateType $type): Certificate
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return CertificateType
     */
    public function getType(): ?CertificateType
    {
        return $this->type;
    }

    /**
     * Set document
     *
     * @param Document|null $document
     *
     * @return Certificate
     */
    public function setDocument(Document $document): Certificate
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument(): ?Document
    {
        return $this->document;
    }
}
