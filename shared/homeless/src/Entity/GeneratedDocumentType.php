<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип построенного документа
 * @ORM\Entity()
 */
class GeneratedDocumentType extends BaseEntity
{
    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * Код
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $code = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return GeneratedDocumentType
     */
    public function setName(?string $name): GeneratedDocumentType
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
     * @return GeneratedDocumentType
     */
    public function setCode(?string $code): GeneratedDocumentType
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
}
