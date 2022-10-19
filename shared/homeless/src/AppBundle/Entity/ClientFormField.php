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
    private ?ClientForm $form = null;

    /**
     * Название поля
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $name = "";

    /**
     * Тип поля (см. константы класса, TYPE_*)
     *
     * @var integer
     * @ORM\Column(type="integer")
     */
    private int $type = 0;

    /**
     * Список вариантов для выбора в поле типа TYPE_OPTION.
     * Варианты разделены переводом строки.
     * Не имеет значения для других типов поля.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $options = null;

    /**
     * @var string[]
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private ?array $flags = null;

    /**
     * @return ClientForm
     */
    public function getForm(): ?ClientForm
    {
        return $this->form;
    }

    /**
     * @param ClientForm $form
     */
    public function setForm(ClientForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param integer $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOptions(): ?string
    {
        return $this->options;
    }

    /**
     * @param string|null $options
     */
    public function setOptions(?string $options)
    {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->getBooleanFlag(self::FLAG_REQUIRED);
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required)
    {
        $this->setBooleanFlag($required, self::FLAG_REQUIRED);
    }

    /**
     * @return bool
     */
    public function isMultiselect(): bool
    {
        return $this->getBooleanFlag(self::FLAG_MULTISELECT);
    }

    /**
     * @param bool $multiselect
     */
    public function setMultiselect(bool $multiselect)
    {
        $this->setBooleanFlag($multiselect, self::FLAG_MULTISELECT);
    }

    /**
     * Флаг означает, что поле формы нельзя удалить или отредактировать через админку.
     *
     * @return bool
     */
    public function isFixed(): bool
    {
        return $this->getBooleanFlag(self::FLAG_FIXED);
    }

    /**
     * @param bool $fixed
     */
    public function setFixed(bool $fixed)
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

    protected function getFlags(): ?array
    {
        return $this->flags;
    }

    protected function setFlags(?array $flags)
    {
        $this->flags = $flags;
    }

    /**
     * Выставляет или убирает флаг `$flagName` в поле `flags`
     *
     * @param bool $flagValue
     * @param string $flagName
     */
    private function setBooleanFlag(bool $flagValue, string $flagName)
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
    private function getBooleanFlag(string $flagName): bool
    {
        $flags = $this->getFlags();
        if ($flags === null) {
            return false;
        }
        return in_array($flagName, $flags);
    }
}
