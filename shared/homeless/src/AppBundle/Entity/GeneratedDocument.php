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
    private ?Client $client = null;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $number = null;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentType")
     */
    private ?GeneratedDocumentType $type = null;

    /**
     * Начальный текст
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentStartText")
     */
    private ?GeneratedDocumentStartText $startText = null;

    /**
     * Конечный текст
     * @ORM\ManyToOne(targetEntity="GeneratedDocumentEndText")
     */
    private ?GeneratedDocumentEndText $endText = null;

    /**
     * Текст
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * Для кого
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $whom = null;

    /**
     * Подпись
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $signature = null;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix(): string
    {
        return 'generated-document';
    }

    public function __toString()
    {
        return $this->type->getName();
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return GeneratedDocument
     */
    public function setNumber(?string $number): GeneratedDocument
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
     * Set text
     *
     * @param string|null $text
     *
     * @return GeneratedDocument
     */
    public function setText(?string $text): GeneratedDocument
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set whom
     *
     * @param string|null $whom
     *
     * @return GeneratedDocument
     */
    public function setWhom(?string $whom): GeneratedDocument
    {
        $this->whom = $whom;

        return $this;
    }

    /**
     * Get whom
     *
     * @return string
     */
    public function getWhom(): ?string
    {
        return $this->whom;
    }

    /**
     * Set signature
     *
     * @param string|null $signature
     *
     * @return GeneratedDocument
     */
    public function setSignature(?string $signature): GeneratedDocument
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return GeneratedDocument
     */
    public function setClient(Client $client): GeneratedDocument
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
     * @param GeneratedDocumentType|null $type
     *
     * @return GeneratedDocument
     */
    public function setType(GeneratedDocumentType $type): GeneratedDocument
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return GeneratedDocumentType
     */
    public function getType(): ?GeneratedDocumentType
    {
        return $this->type;
    }

    /**
     * Set startText
     *
     * @param GeneratedDocumentStartText|null $startText
     *
     * @return GeneratedDocument
     */
    public function setStartText(GeneratedDocumentStartText $startText): GeneratedDocument
    {
        $this->startText = $startText;

        return $this;
    }

    /**
     * Get startText
     *
     * @return GeneratedDocumentStartText
     */
    public function getStartText(): ?GeneratedDocumentStartText
    {
        return $this->startText;
    }

    /**
     * Set endText
     *
     * @param GeneratedDocumentEndText|null $endText
     *
     * @return GeneratedDocument
     */
    public function setEndText(GeneratedDocumentEndText $endText): GeneratedDocument
    {
        $this->endText = $endText;

        return $this;
    }

    /**
     * Get endText
     *
     * @return GeneratedDocumentEndText
     */
    public function getEndText(): ?GeneratedDocumentEndText
    {
        return $this->endText;
    }
}
