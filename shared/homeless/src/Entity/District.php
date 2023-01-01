<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Район региона РФ
 * @ORM\Entity()
 */
class District extends BaseEntity
{
    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * Регион
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="districts")
     */
    private ?Region $region = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return District
     */
    public function setName(?string $name): District
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
     * Set region
     *
     * @param Region|null $region
     *
     * @return District
     */
    public function setRegion(Region $region): District
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }
}
