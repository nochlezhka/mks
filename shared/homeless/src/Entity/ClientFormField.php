<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Поле настраиваемой формы
 */
#[ORM\Entity]
class ClientFormField extends BaseEntity
{
    // ID фиксированного поля формы анкеты проживающего
    public const RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID = 1;

    public const TYPE_TEXT = 1;
    public const TYPE_OPTION = 2;
    public const TYPE_CHECKBOX = 3;

    public const FLAG_REQUIRED = 'required';
    // обозначает, что у селекта может быть множественный выбор
    public const FLAG_MULTISELECT = 'multiselect';
    // если поле обозначено как "fixed", его нельзя изменять или удалять через админку
    public const FLAG_FIXED = 'fixed';

    /**
     * Форма, которой принадлежит поле
     */
    #[ORM\ManyToOne(targetEntity: ClientForm::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientForm $form = null;

    #[ORM\Column(type: 'string')]
    private string $name = '';

    /**
     * Тип поля (см. константы класса, TYPE_*)
     */
    #[ORM\Column(type: 'integer')]
    private int $type = 0;

    /**
     * Список вариантов для выбора в поле типа TYPE_OPTION.
     * Варианты разделены переводом строки.
     * Не имеет значения для других типов поля.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $options = null;

    /** @var array<string> */
    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $flags = null;

    public function __toString(): string
    {
        return 'Поле '.$this->name;
    }

    public function getForm(): ?ClientForm
    {
        return $this->form;
    }

    public function setForm(ClientForm $form): void
    {
        $this->form = $form;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): void
    {
        $this->options = $options;
    }

    public function isRequired(): bool
    {
        return $this->getBooleanFlag(self::FLAG_REQUIRED);
    }

    public function setRequired(bool $required): void
    {
        $this->setBooleanFlag($required, self::FLAG_REQUIRED);
    }

    public function isMultiselect(): bool
    {
        return $this->getBooleanFlag(self::FLAG_MULTISELECT);
    }

    public function setMultiselect(bool $multiselect): void
    {
        $this->setBooleanFlag($multiselect, self::FLAG_MULTISELECT);
    }

    /**
     * Флаг означает, что поле формы нельзя удалить или отредактировать через админку.
     */
    public function isFixed(): bool
    {
        return $this->getBooleanFlag(self::FLAG_FIXED);
    }

    public function setFixed(bool $fixed): void
    {
        $this->setBooleanFlag($fixed, self::FLAG_FIXED);
    }

    protected function getFlags(): ?array
    {
        return $this->flags;
    }

    protected function setFlags(?array $flags): void
    {
        $this->flags = $flags;
    }

    /**
     * Есть ли флаг `$flagName` в поле `flags`
     */
    private function getBooleanFlag(string $flagName): bool
    {
        $flags = $this->getFlags();
        if ($flags === null) {
            return false;
        }

        return \in_array($flagName, $flags, true);
    }

    /**
     * Выставляет или убирает флаг `$flagName` в поле `flags`
     */
    private function setBooleanFlag(bool $flagValue, string $flagName): void
    {
        $flags = $this->getFlags() ?? [];
        if ($flagValue) {
            $this->setFlags(array_unique([...$flags, $flagName]));
        } else {
            $this->setFlags(array_filter($flags, static fn ($el): bool => $el !== $flagName));
        }
    }
}
