<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Поле настраиваемой формы
 *
 * @ORM\Entity
 */
class ClientFormField extends BaseEntity
{
    // ID фиксированного поля формы анкеты проживающего
    const RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID = 1;

    const TYPE_TEXT = 1;
    const TYPE_OPTION = 2;
    const TYPE_CHECKBOX = 3;

    const FLAG_REQUIRED = "required";
    // обозначает, что у селекта может быть множественный выбор
    const FLAG_MULTISELECT = "multiselect";
    // если поле обозначено как "fixed", его нельзя изменять или удалять через админку
    const FLAG_FIXED = "fixed";

    /**
     * Форма, которой принадлежит поле
     *
     * @var ClientForm
     * @ORM\ManyToOne(targetEntity="ClientForm", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $form;

    /**
     * Название поля
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Тип поля (см. константы класса, TYPE_*)
     *
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * Список вариантов для выбора в поле типа TYPE_OPTION.
     * Варианты разделены переводом строки.
     * Не имеет значения для других типов поля.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @var string[]
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $flags;

    /**
     * @return ClientForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param ClientForm $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->getBooleanFlag(self::FLAG_REQUIRED);
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->setBooleanFlag($required, self::FLAG_REQUIRED);
    }

    /**
     * @return bool
     */
    public function isMultiselect()
    {
        return $this->getBooleanFlag(self::FLAG_MULTISELECT);
    }

    /**
     * @param bool $multiselect
     */
    public function setMultiselect($multiselect)
    {
        $this->setBooleanFlag($multiselect, self::FLAG_MULTISELECT);
    }

    /**
     * Флаг означает, что поле формы нельзя удалить или отредактировать через админку.
     *
     * @return bool
     */
    public function isFixed()
    {
        return $this->getBooleanFlag(self::FLAG_FIXED);
    }

    /**
     * @param bool $fixed
     */
    public function setFixed($fixed)
    {
        $this->setBooleanFlag($fixed, self::FLAG_FIXED);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Поле ".$this->getName();
    }

    protected function getFlags()
    {
        return $this->flags;
    }

    protected function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Выставляет или убирает флаг `$flagName` в поле `flags`
     *
     * @param bool $flagValue
     * @param string $flagName
     */
    private function setBooleanFlag($flagValue, $flagName)
    {
        $flags = $this->getFlags();
        if ($flags === null) {
            $flags = [];
        }
        if ($flagValue) {
            $this->setFlags(array_unique(array_merge($flags, [$flagName])));
        } else {
            $this->setFlags(array_filter($flags, function($el) use ($flagName) {return $el != $flagName; }));
        }
    }

    /**
     * Есть ли флаг `$flagName` в поле `flags`
     *
     * @param string $flagName
     * @return bool
     */
    private function getBooleanFlag($flagName)
    {
        $flags = $this->getFlags();
        if ($flags === null) {
            return false;
        }
        return in_array($flagName, $flags);
    }
}
