<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Вариант значения дополнительного поля клиента
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientFieldOptionRepository")
 */
class ClientFieldOption extends BaseEntity
{
    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * Если - true, то при заполнении не может быть выбрано только
     * одно это значение, необходимо указать еще какое-нибудь
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notSingle = false;

    /**
     * Поле
     * @ORM\ManyToOne(targetEntity="ClientField", inversedBy="options")
     */
    private $field;

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ClientFieldOption
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
     * Set notSingle
     *
     * @param boolean $notSingle
     *
     * @return ClientFieldOption
     */
    public function setNotSingle($notSingle)
    {
        $this->notSingle = $notSingle;

        return $this;
    }

    /**
     * Get notSingle
     *
     * @return boolean
     */
    public function getNotSingle()
    {
        return $this->notSingle;
    }

    /**
     * Set field
     *
     * @param \AppBundle\Entity\ClientField $field
     *
     * @return ClientFieldOption
     */
    public function setField(ClientField $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \AppBundle\Entity\ClientField
     */
    public function getField()
    {
        return $this->field;
    }
}
