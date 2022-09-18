<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Регион РФ
 * @ORM\Entity()
 */
class Region extends BaseEntity
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

    /**
     * Районы
     * @ORM\OneToMany(targetEntity="District", mappedBy="region")
     */
    private ArrayCollection $districts;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->districts = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return Region
     */
    public function setName(?string $name): Region
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
     * @return Region
     */
    public function setShortName(?string $shortName): Region
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

    /**
     * Add district
     *
     * @param District $district
     *
     * @return Region
     */
    public function addDistrict(District $district): Region
    {
        $this->districts[] = $district;

        return $this;
    }

    /**
     * Remove district
     *
     * @param District $district
     */
    public function removeDistrict(District $district)
    {
        $this->districts->removeElement($district);
    }

    /**
     * Get districts
     *
     * @return Collection
     */
    public function getDistricts()
    {
        return $this->districts;
    }
}
