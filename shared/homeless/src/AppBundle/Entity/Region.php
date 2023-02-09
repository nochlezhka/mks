<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    private $name;

    /**
     * Сокращенное название
     * @ORM\Column(type="string", nullable=true)
     */
    private $shortName;

    /**
     * Районы
     * @ORM\OneToMany(targetEntity="District", mappedBy="region")
     */
    private $districts;

    public function __toString()
    {
        return (string)$this->name;
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
     * @param string $name
     *
     * @return Region
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Region
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Add district
     *
     * @param \AppBundle\Entity\District $district
     *
     * @return Region
     */
    public function addDistrict(District $district)
    {
        $this->districts[] = $district;

        return $this;
    }

    /**
     * Remove district
     *
     * @param \AppBundle\Entity\District $district
     */
    public function removeDistrict(District $district)
    {
        $this->districts->removeElement($district);
    }

    /**
     * Get districts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDistricts()
    {
        return $this->districts;
    }
}
