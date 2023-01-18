<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип документа
 */
#[ORM\Entity]
class DocumentType extends BaseEntity
{
    /**
     * Для постановки на учет
     */
    const TYPE_REGISTRATION = 1;

    /**
     * Другой
     */
    const TYPE_OTHER = 2;

    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Тип
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $type = self::TYPE_OTHER;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return DocumentType
     */
    public function setName(?string $name): DocumentType
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
     * Set type
     *
     * @param int|null $type
     *
     * @return DocumentType
     */
    public function setType(?int $type): DocumentType
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType(): ?int
    {
        return $this->type;
    }
}
