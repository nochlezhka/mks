<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Repository\ClientFieldValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Значение дополнительного поля клиента
 */
#[UniqueConstraint(name: 'value_unique', columns: ['field_id', 'client_id'])]
#[ORM\Entity(repositoryClass: ClientFieldValueRepository::class)]
#[Vich\Uploadable]
class ClientFieldValue extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: ClientField::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ClientField $field = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'fieldValues')]
    private ?Client $client = null;

    /**
     * Значение поля - текст
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text = null;

    /**
     * Значение поля - дата/время
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $datetime = null;

    /**
     * Вариант значения (если поле не multiple)
     */
    #[ORM\ManyToOne(targetEntity: ClientFieldOption::class)]
    private ?ClientFieldOption $option = null;

    /**
     * Варианты значений (если поле multiple)
     */
    #[ORM\ManyToMany(targetEntity: ClientFieldOption::class)]
    private Collection $options;

    /**
     * Имя файла для файлового поля
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?\DateTimeImmutable $filename = null;

    /**
     * Значение поля - файл
     */
    #[Vich\UploadableField(mapping: 'client_field_file', fileNameProperty: 'filename')]
    private ?File $file = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function setFile(?File $file = null): self
    {
        $this->file = $file;

        if ($file) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Возвращает значение в зависимости от типа поля
     */
    public function getValue(): ClientFieldOption|\DateTimeImmutable|Collection|string|null
    {
        return match ($this->field->getType()) {
            ClientField::TYPE_TEXT => $this->getText(),
            ClientField::TYPE_DATETIME => $this->getDatetime(),
            ClientField::TYPE_FILE => $this->getFile(),
            ClientField::TYPE_OPTION => $this->field->isMultiple()
                ? $this->getOptions()
                : $this->getOption(),
            default => null,
        };
    }

    /**
     * Устанавливает значение в зависимости от типа поля
     */
    public function setValue(ClientFieldOption|\DateTimeImmutable|Collection|string|null $value): self
    {
        return match ($this->field->getType()) {
            ClientField::TYPE_TEXT => $this->setText($value),
            ClientField::TYPE_DATETIME => $this->setDatetime($value),
            ClientField::TYPE_FILE => $this->setFile($value),
            ClientField::TYPE_OPTION => $this->field->isMultiple()
                ? $this->setOptions($value)
                : $this->setOption($value),
            default => $this,
        };
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDatetime(): ?\DateTimeImmutable
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeImmutable $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getFilename(): ?\DateTimeImmutable
    {
        return $this->filename;
    }

    public function setFilename(?\DateTimeImmutable $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getField(): ?ClientField
    {
        return $this->field;
    }

    public function setField(?ClientField $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOption(): ?ClientFieldOption
    {
        return $this->option;
    }

    public function setOption(?ClientFieldOption $option): self
    {
        $this->option = $option;

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

    public function hasOption(ClientFieldOption $option): bool
    {
        return $this->options->contains($option);
    }

    public function setOptions(Collection $options): self
    {
        $this->options = $options;

        return $this;
    }
}
