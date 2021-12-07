<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Branch
 *
 * @ORM\Table(name="branches")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BranchRepository")
 */
class Branch extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * Пользователи с данной должностью
     * @ORM\OneToMany(targetEntity="Application\Sonata\UserBundle\Entity\User", mappedBy="branch")
     */
    private $users;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $orgNameShort;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $orgName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $orgDescription;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $orgDescriptionShort;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $orgCity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $orgContactsFull;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $dispensaryName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $dispensaryAddress;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $dispensaryPhone;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $employmentName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $employmentAddress;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $employmentInspection;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $sanitationName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $sanitationAddress;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $sanitationTime;
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Branch
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
     * Add user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     *
     * @return Branch
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Remove user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getOrgNameShort()
    {
        return $this->orgNameShort;
    }

    /**
     * @param string $orgNameShort
     */
    public function setOrgNameShort($orgNameShort)
    {
        $this->orgNameShort = $orgNameShort;
    }

    /**
     * @return string
     */
    public function getOrgName()
    {
        return $this->orgName;
    }

    /**
     * @param string $orgName
     */
    public function setOrgName($orgName)
    {
        $this->orgName = $orgName;
    }

    /**
     * @return string
     */
    public function getOrgDescription()
    {
        return $this->orgDescription;
    }

    /**
     * @param string $orgDescription
     */
    public function setOrgDescription($orgDescription)
    {
        $this->orgDescription = $orgDescription;
    }

    /**
     * @return string
     */
    public function getOrgDescriptionShort()
    {
        return $this->orgDescriptionShort;
    }

    /**
     * @param string $orgDescriptionShort
     */
    public function setOrgDescriptionShort($orgDescriptionShort)
    {
        $this->orgDescriptionShort = $orgDescriptionShort;
    }

    /**
     * @return string
     */
    public function getOrgCity()
    {
        return $this->orgCity;
    }

    /**
     * @param string $orgCity
     */
    public function setOrgCity($orgCity)
    {
        $this->orgCity = $orgCity;
    }

    /**
     * @return string
     */
    public function getOrgContactsFull()
    {
        return $this->orgContactsFull;
    }

    /**
     * @param string $orgContactsFull
     */
    public function setOrgContactsFull($orgContactsFull)
    {
        $this->orgContactsFull = $orgContactsFull;
    }

    /**
     * @return string
     */
    public function getDispensaryName()
    {
        return $this->dispensaryName;
    }

    /**
     * @param string $dispensaryName
     */
    public function setDispensaryName($dispensaryName)
    {
        $this->dispensaryName = $dispensaryName;
    }

    /**
     * @return string
     */
    public function getDispensaryAddress()
    {
        return $this->dispensaryAddress;
    }

    /**
     * @param string $dispensaryAddress
     */
    public function setDispensaryAddress($dispensaryAddress)
    {
        $this->dispensaryAddress = $dispensaryAddress;
    }

    /**
     * @return string
     */
    public function getDispensaryPhone()
    {
        return $this->dispensaryPhone;
    }

    /**
     * @param string $dispensaryPhone
     */
    public function setDispensaryPhone($dispensaryPhone)
    {
        $this->dispensaryPhone = $dispensaryPhone;
    }

    /**
     * @return string
     */
    public function getEmploymentName()
    {
        return $this->employmentName;
    }

    /**
     * @param string $employmentName
     */
    public function setEmploymentName($employmentName)
    {
        $this->employmentName = $employmentName;
    }

    /**
     * @return string
     */
    public function getEmploymentAddress()
    {
        return $this->employmentAddress;
    }

    /**
     * @param string $employmentAddress
     */
    public function setEmploymentAddress($employmentAddress)
    {
        $this->employmentAddress = $employmentAddress;
    }

    /**
     * @return string
     */
    public function getEmploymentInspection()
    {
        return $this->employmentInspection;
    }

    /**
     * @param string $employmentInspection
     */
    public function setEmploymentInspection($employmentInspection)
    {
        $this->employmentInspection = $employmentInspection;
    }

    /**
     * @return string
     */
    public function getSanitationName()
    {
        return $this->sanitationName;
    }

    /**
     * @param string $sanitationName
     */
    public function setSanitationName($sanitationName)
    {
        $this->sanitationName = $sanitationName;
    }

    /**
     * @return string
     */
    public function getSanitationAddress()
    {
        return $this->sanitationAddress;
    }

    /**
     * @param string $sanitationAddress
     */
    public function setSanitationAddress($sanitationAddress)
    {
        $this->sanitationAddress = $sanitationAddress;
    }

    /**
     * @return string
     */
    public function getSanitationTime()
    {
        return $this->sanitationTime;
    }

    /**
     * @param string $sanitationTime
     */
    public function setSanitationTime($sanitationTime)
    {
        $this->sanitationTime = $sanitationTime;
    }
}
