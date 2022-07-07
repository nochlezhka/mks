<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

/**
 * Дополнительное поле клиента
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientFieldRepository")
 */
class ClientField extends BaseEntity
{
    /**
     * Текст
     */
    const TYPE_TEXT = 1;

    /**
     * Выбор значения(-ий) из списка
     */
    const TYPE_OPTION = 2;

    /**
     * Файл
     */
    const TYPE_FILE = 3;

    /**
     * Дата и время
     */
    const TYPE_DATETIME = 4;

    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * Символьный код
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    /**
     * Включено
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled = true;

    /**
     * Включено для бездомных
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabledForHomeless = true;

    /**
     * Тип
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * Обязательное
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $required = false;

    /**
     * Обязательное для бездомных
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mandatoryForHomeless = false;

    /**
     * Допускается выбор нескольких вариантов одновременно
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $multiple = false;

    /**
     * Подсказка
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * Поле
     * @ORM\OneToMany(targetEntity="ClientFieldOption", mappedBy="field")
     */
    private $options;

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Массив вариантов значений
     * @return array
     */
    public function getOptionsArray(): array
    {
        $options = [];

        foreach ($this->options as $option) {
            $options[$option->getName()] = $option->getId();
        }

        return $options;
    }

    /**
     * Тип поля для отображения в анкете
     * @return ClientFieldOption|\DateTime|\Doctrine\Common\Collections\Collection|null|string
     */
    public function getShowFieldType()
    {
        if ($this->type == self::TYPE_TEXT) {
            return 'textarea';
        }

        if ($this->type == self::TYPE_DATETIME) {
            return 'datetime';
        }

        if ($this->type == self::TYPE_FILE) {
            return 'text';
        }

        if ($this->type == self::TYPE_OPTION) {
            if ($this->multiple) {
                return 'array';
            }

            return 'choice';
        }

        return null;
    }

    /**
     * Тип поля для формы
     * @return ClientFieldOption|\DateTime|\Doctrine\Common\Collections\Collection|null|string
     */
    public function getFormFieldType()
    {
        if ($this->type == self::TYPE_TEXT) {
            return 'textarea';
        }

        if ($this->type == self::TYPE_DATETIME) {
            return BirthdayType::class;
        }

        if ($this->type == self::TYPE_FILE) {
            return 'app_file';
        }

        if ($this->type == self::TYPE_OPTION) {
            return 'entity';
        }

        return null;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return ClientField
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
     * @return ClientField
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
     * @return ClientField
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

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return ClientField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return ClientField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     *
     * @return ClientField
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * Add option
     *
     * @param \AppBundle\Entity\ClientFieldOption $option
     *
     * @return ClientField
     */
    public function addOption(ClientFieldOption $option)
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param \AppBundle\Entity\ClientFieldOption $option
     */
    public function removeOption(ClientFieldOption $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ClientField
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function getMandatoryForHomeless()
    {
        return $this->mandatoryForHomeless || $this->required;
    }

    /**
     * @param bool $mandatoryForHomeless
     * @return ClientField
     */
    public function setMandatoryForHomeless($mandatoryForHomeless)
    {
        $this->mandatoryForHomeless = $mandatoryForHomeless;

        return $this;
    }

    /**
     * Get enabledForHomeless
     *
     * @return mixed
     */
    public function getEnabledForHomeless()
    {
        return $this->enabledForHomeless || $this->enabled;
    }

    /**
     * Set enabledForHomeless
     *
     * @param mixed $enabledForHomeless
     *
     * @return ClientField
     */
    public function setEnabledForHomeless($enabledForHomeless)
    {
        $this->enabledForHomeless = $enabledForHomeless;

        return $this;
    }
}
