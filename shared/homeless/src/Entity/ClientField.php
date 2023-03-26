<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Form\Type\AppFileType;
use App\Repository\ClientFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Дополнительное поле клиента
 */
#[ORM\Entity(repositoryClass: ClientFieldRepository::class)]
#[UniqueEntity(
    fields: ['code'],
    message: 'code.unique',
    errorPath: 'code',
)]
class ClientField extends BaseEntity
{
    public const TYPE_TEXT = 1;
    public const TYPE_OPTION = 2; // Выбор значения(-ий) из списка
    public const TYPE_FILE = 3;
    public const TYPE_DATETIME = 4;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: 'code.not_blank')]
    #[Assert\Regex(
        pattern: '/(.*)additionalField(.*)/i',
        message: 'code.regex',
        match: false,
    )]
    private ?string $code = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $enabled = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $enabledForHomeless = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $type = null;

    #[ORM\Column(type: 'boolean')]
    private bool $required = false;

    #[ORM\Column(type: 'boolean')]
    private bool $mandatoryForHomeless = false;

    #[ORM\Column(type: 'boolean')]
    private bool $multiple = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: ClientFieldOption::class)]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    /**
     * Массив вариантов значений
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
     */
    public function getShowFieldType(): ClientFieldOption|\DateTimeImmutable|Collection|string|null
    {
        return match ($this->type) {
            self::TYPE_TEXT => TextareaType::class,
            self::TYPE_DATETIME => 'datetime',
            self::TYPE_FILE => 'text',
            self::TYPE_OPTION => $this->multiple
                ? 'array'
                : ChoiceType::class,
            default => null,
        };
    }

    /**
     * Тип поля для формы
     */
    public function getFormFieldType(): ?string
    {
        return match ($this->type) {
            self::TYPE_TEXT => TextareaType::class,
            self::TYPE_DATETIME => BirthdayType::class,
            self::TYPE_FILE => AppFileType::class,
            self::TYPE_OPTION => EntityType::class,
            default => null,
        };
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(ClientFieldOption $option): self
    {
        $this->options->add($option);

        return $this;
    }

    public function removeOption(ClientFieldOption $option): void
    {
        $this->options->removeElement($option);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isMandatoryForHomeless(): bool
    {
        return $this->mandatoryForHomeless || $this->required;
    }

    public function setMandatoryForHomeless(bool $mandatoryForHomeless): self
    {
        $this->mandatoryForHomeless = $mandatoryForHomeless;

        return $this;
    }

    public function isEnabledForHomeless(): bool
    {
        return $this->enabledForHomeless || $this->enabled;
    }

    public function setEnabledForHomeless(bool $enabledForHomeless): self
    {
        $this->enabledForHomeless = $enabledForHomeless;

        return $this;
    }
}
