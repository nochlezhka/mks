<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Значение дополнительного поля клиента
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="value_unique", columns={"field_id", "client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientFieldValueRepository")
 * @Vich\Uploadable
 */
class ClientFieldValue extends BaseEntity
{
    /**
     * Поле
     * @ORM\ManyToOne(targetEntity="ClientField")
     */
    private ?ClientField $field = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="fieldValues")
     */
    private ?Client $client = null;

    /**
     * Значение поля - текст
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * Значение поля - дата/время
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $datetime = null;

    /**
     * Вариант значения (если поле не multiple)
     * @ORM\ManyToOne(targetEntity="ClientFieldOption")
     */
    private ?ClientFieldOption $option = null;

    /**
     * Варианты значений (если поле multiple)
     * @ORM\ManyToMany(targetEntity="ClientFieldOption")
     */
    private Collection $options;

    /**
     * Имя файла для файлового поля
     * @ORM\Column(type="string", nullable=true)
     */
    private ?DateTime $filename = null;

    /**
     * Значение поля - файл
     * @Vich\UploadableField(mapping="client_field_file", fileNameProperty="filename")
     */
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file = null): ClientFieldValue
    {
        $this->file = $file;

        if ($file) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * Возвращает значение в зависимости от типа поля
     * @return ClientFieldOption|DateTime|Collection|null|string
     */
    public function getValue()
    {
        $type = $this->field->getType();

        if ($type == ClientField::TYPE_TEXT) {
            return $this->getText();
        }

        if ($type == ClientField::TYPE_DATETIME) {
            return $this->getDatetime();
        }

        if ($type == ClientField::TYPE_FILE) {
            return $this->getFile();
        }

        if ($type == ClientField::TYPE_OPTION) {
            if ($this->field->getMultiple()) {
                return $this->getOptions();
            }

            return $this->getOption();
        }

        return null;
    }

    /**
     * Устанавливает значение в зависимости от типа поля
     * @param $value
     * @return ClientFieldValue
     */
    public function setValue($value): ?ClientFieldValue
    {
        $type = $this->field->getType();

        if ($type == ClientField::TYPE_TEXT) {
            return $this->setText($value);
        }

        if ($type == ClientField::TYPE_DATETIME) {
            return $this->setDatetime($value);
        }

        if ($type == ClientField::TYPE_FILE) {
            return $this->setFile($value);
        }

        if ($type == ClientField::TYPE_OPTION) {
            if ($this->field->getMultiple()) {
                return $this->setOptions($value);
            }

            return $this->setOption($value);
        }

        return null;
    }

    /**
     * Set text
     *
     * @param string|null $text
     *
     * @return ClientFieldValue
     */
    public function setText(?string $text): ClientFieldValue
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set datetime
     *
     * @param DateTime|null $datetime
     *
     * @return ClientFieldValue
     */
    public function setDatetime(?DateTime $datetime): ClientFieldValue
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return DateTime
     */
    public function getDatetime(): ?DateTime
    {
        return $this->datetime;
    }

    /**
     * Set filename
     *
     * @param DateTime|null $filename
     *
     * @return ClientFieldValue
     */
    public function setFilename(?DateTime $filename): ClientFieldValue
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return DateTime
     */
    public function getFilename(): ?DateTime
    {
        return $this->filename;
    }

    /**
     * Set field
     *
     * @param ClientField|null $field
     *
     * @return ClientFieldValue
     */
    public function setField(ClientField $field): ClientFieldValue
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

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return ClientFieldValue
     */
    public function setClient(Client $client): ClientFieldValue
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Set option
     *
     * @param ClientFieldOption|null $option
     *
     * @return ClientFieldValue
     */
    public function setOption(ClientFieldOption $option): ClientFieldValue
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return ClientFieldOption
     */
    public function getOption(): ?ClientFieldOption
    {
        return $this->option;
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
     * @return ClientFieldValue
     */
    public function addOption(ClientFieldOption $option): ClientFieldValue
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

    public function hasOption(ClientFieldOption $option): bool
    {
        return $this->options->contains($option);
    }

    /**
     * Get options
     *
     * @param Collection $options
     * @return ClientFieldValue
     */
    public function setOptions(Collection $options): ClientFieldValue
    {
        $this->options = $options;

        return $this;
    }
}
