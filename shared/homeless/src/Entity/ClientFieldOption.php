<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Вариант значения дополнительного поля клиента
 */
#[ORM\Entity]
class ClientFieldOption extends BaseEntity
{
    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Если - true, то при заполнении не может быть выбрано только
     * одно это значение, необходимо указать еще какое-нибудь
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $notSingle = false;

    /**
     * Поле
     */
    #[ORM\ManyToOne(targetEntity: ClientField::class, inversedBy: "options")]
    private ?ClientField $field = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ClientFieldOption
     */
    public function setName(?string $name): ClientFieldOption
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
     * Set notSingle
     *
     * @param boolean $notSingle
     *
     * @return ClientFieldOption
     */
    public function setNotSingle(?bool $notSingle): ClientFieldOption
    {
        $this->notSingle = $notSingle;

        return $this;
    }

    /**
     * Get notSingle
     *
     * @return boolean
     */
    public function getNotSingle(): ?bool
    {
        return $this->notSingle;
    }

    /**
     * Set field
     *
     * @param ClientField|null $field
     *
     * @return ClientFieldOption
     */
    public function setField(ClientField $field): ClientFieldOption
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return ClientField
     */
    public function getField(): ?ClientField
    {
        return $this->field;
    }
}
