<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Причины выбытия
 * @ORM\Entity()
 */
class ShelterLeavingReason extends BaseEntity
{
    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    public function __toString()
    {
        return (string)$this->name;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return ShelterLeavingReason
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

}
