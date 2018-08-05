<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип документа
 * @ORM\Entity()
 */
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * Тип
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type = self::TYPE_OTHER;

    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DocumentType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return DocumentType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}
