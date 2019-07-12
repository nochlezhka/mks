<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Для настройки отображения пунктов меню в анкете клиента
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MenuItemRepository")
 * @UniqueEntity("code")
 */
class MenuItem extends BaseEntity
{
    const CODE_SHELTER_HISTORY = 'shelter_history';
    const CODE_CERTIFICATE = 'certificate';
    const CODE_GENERATED_DOCUMENT = 'generated_document';

    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * Код
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $code;

    /**
     * Включено
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled = true;

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MenuItem
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
     * Set code
     *
     * @param string $code
     *
     * @return MenuItem
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return MenuItem
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
