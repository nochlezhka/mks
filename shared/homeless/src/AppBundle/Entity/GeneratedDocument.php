<?php

namespace AppBundle\Entity;

use AppBundle\Service\DownloadableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Построенный документ
 * @ORM\Entity()
 */
class GeneratedDocument extends BaseEntity implements DownloadableInterface
{
    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="generatedDocuments")
     */
    private $client;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentType")
     */
    private $type;

    /**
     * Начальный текст
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentStartText")
     */
    private $startText;

    /**
     * Конечный текст
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentEndText")
     */
    private $endText;

    /**
     * Текст
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * Для кого
     * @ORM\Column(type="text", nullable=true)
     */
    private $whom;

    /**
     * Подпись
     * @ORM\Column(type="text", nullable=true)
     */
    private $signature;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix()
    {
        return 'generated-document';
    }

    public function __toString()
    {
        if ($this->type instanceof GeneratedDocumentType) {
            return (string)$this->type->getName();
        }

        return '';
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return GeneratedDocument
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
     * Set text
     *
     * @param string $text
     *
     * @return GeneratedDocument
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set whom
     *
     * @param string $whom
     *
     * @return GeneratedDocument
     */
    public function setWhom($whom)
    {
        $this->whom = $whom;

        return $this;
    }

    /**
     * Get whom
     *
     * @return string
     */
    public function getWhom()
    {
        return $this->whom;
    }

    /**
     * Set signature
     *
     * @param string $signature
     *
     * @return GeneratedDocument
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return GeneratedDocument
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
     * @param \AppBundle\Entity\GeneratedDocumentType $type
     *
     * @return GeneratedDocument
     */
    public function setType(GeneratedDocumentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\GeneratedDocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set startText
     *
     * @param \AppBundle\Entity\GeneratedDocumentStartText $startText
     *
     * @return GeneratedDocument
     */
    public function setStartText(GeneratedDocumentStartText $startText = null)
    {
        $this->startText = $startText;

        return $this;
    }

    /**
     * Get startText
     *
     * @return \AppBundle\Entity\GeneratedDocumentStartText
     */
    public function getStartText()
    {
        return $this->startText;
    }

    /**
     * Set endText
     *
     * @param \AppBundle\Entity\GeneratedDocumentEndText $endText
     *
     * @return GeneratedDocument
     */
    public function setEndText(GeneratedDocumentEndText $endText = null)
    {
        $this->endText = $endText;

        return $this;
    }

    /**
     * Get endText
     *
     * @return \AppBundle\Entity\GeneratedDocumentEndText
     */
    public function getEndText()
    {
        return $this->endText;
    }
}
