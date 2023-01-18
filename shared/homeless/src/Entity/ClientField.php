<?php

namespace App\Entity;

use App\Form\Type\AppFileType;
use App\Repository\ClientFieldRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Дополнительное поле клиента
 */
#[ORM\Entity(repositoryClass: ClientFieldRepository::class)]
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
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Символьный код
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $code = null;

    /**
     * Включено
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $enabled = true;

    /**
     * Включено для бездомных
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $enabledForHomeless = true;

    /**
     * Тип
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $type = null;

    /**
     * Обязательное
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $required = false;

    /**
     * Обязательное для бездомных
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $mandatoryForHomeless = false;

    /**
     * Допускается выбор нескольких вариантов одновременно
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $multiple = false;

    /**
     * Подсказка
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $description = null;

    /**
     * Поле
     */
    #[ORM\OneToMany(mappedBy: "field", targetEntity: ClientFieldOption::class)]
    private Collection $options;

    public function __toString()
    {
        return $this->name;
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
     * @return ClientFieldOption|DateTime|Collection|null|string
     */
    public function getShowFieldType()
    {
        if ($this->type == self::TYPE_TEXT) {
            return TextareaType::class;
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

            return ChoiceType::class;
        }

        return null;
    }

    /**
     * Тип поля для формы
     */
    public function getFormFieldType(): ?string
    {
        if ($this->type == self::TYPE_TEXT) {
            return TextareaType::class;
        }

        if ($this->type == self::TYPE_DATETIME) {
            return BirthdayType::class;
        }

        if ($this->type == self::TYPE_FILE) {
            return AppFileType::class;
        }

        if ($this->type == self::TYPE_OPTION) {
            return EntityType::class;
        }

        return null;
    }


    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ClientField
     */
    public function setName(?string $name): ClientField
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
     * Set code
     *
     * @param string|null $code
     *
     * @return ClientField
     */
    public function setCode(?string $code): ClientField
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(): ?string
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
    public function setEnabled(?bool $enabled): ClientField
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * Set type
     *
     * @param int|null $type
     *
     * @return ClientField
     */
    public function setType(?int $type): ClientField
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType(): ?int
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
    public function setRequired(?bool $required): ClientField
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired(): ?bool
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
    public function setMultiple(?bool $multiple): ClientField
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple(): ?bool
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
     * @param ClientFieldOption $option
     *
     * @return ClientField
     */
    public function addOption(ClientFieldOption $option): ClientField
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param ClientFieldOption $option
     */
    public function removeOption(ClientFieldOption $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * Get options
     *
     * @return Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return ClientField
     */
    public function setDescription(?string $description): ClientField
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function getMandatoryForHomeless(): ?bool
    {
        return $this->mandatoryForHomeless || $this->required;
    }

    /**
     * @param bool $mandatoryForHomeless
     * @return ClientField
     */
    public function setMandatoryForHomeless(?bool $mandatoryForHomeless): ClientField
    {
        $this->mandatoryForHomeless = $mandatoryForHomeless;

        return $this;
    }

    /**
     * Get enabledForHomeless
     */
    public function getEnabledForHomeless(): ?bool
    {
        return $this->enabledForHomeless || $this->enabled;
    }

    /**
     * Set enabledForHomeless
     *
     * @param bool|null $enabledForHomeless
     * @return ClientField
     */
    public function setEnabledForHomeless(?bool $enabledForHomeless): ClientField
    {
        $this->enabledForHomeless = $enabledForHomeless;

        return $this;
    }
}
