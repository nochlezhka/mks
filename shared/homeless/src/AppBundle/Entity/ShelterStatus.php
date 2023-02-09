<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Статус проживания в приюте
 * @ORM\Entity()
 */
class ShelterStatus extends BaseEntity
{
    /**
     * SyncId статуса "проживает"
     */
    const IN_PROCESS = 1;

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
     * @return ShelterStatus
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
