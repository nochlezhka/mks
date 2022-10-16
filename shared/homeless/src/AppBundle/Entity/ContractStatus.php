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
    private ?string $name = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ContractStatus
     */
    public function setName(?string $name): ContractStatus
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
}
