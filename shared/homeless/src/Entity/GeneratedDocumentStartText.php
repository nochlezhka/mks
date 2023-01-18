<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Вараиант начального текста построенного документа
 */
#[ORM\Entity]
class GeneratedDocumentStartText extends BaseEntity
{
    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Код
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $code = null;

    /**
     * Текст
     */
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $text = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return GeneratedDocumentStartText
     */
    public function setName(?string $name): GeneratedDocumentStartText
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string|null $code
     *
     * @return GeneratedDocumentStartText
     */
    public function setCode(?string $code): GeneratedDocumentStartText
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set text
     *
     * @param string|null $text
     *
     * @return GeneratedDocumentStartText
     */
    public function setText(?string $text): GeneratedDocumentStartText
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
}
