<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Статус договора
 * @ORM\Entity()
 */
class ContractStatus extends BaseEntity
{
    /**
     * SyncId статуса "В процессе выполнения"
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
     * @return ContractStatus
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
