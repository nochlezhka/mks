<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип пункта договора (сервисного плана)
 * @ORM\Entity()
 */
class ContractItemType extends BaseEntity
{
    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name;

    /**
     * Сокращенное название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $shortName;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ContractItemType
     */
    public function setName(?string $name): ContractItemType
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
     * Set shortName
     *
     * @param string|null $shortName
     *
     * @return ContractItemType
     */
    public function setShortName(?string $shortName): ContractItemType
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }
}
