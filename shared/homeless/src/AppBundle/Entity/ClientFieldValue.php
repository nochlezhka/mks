<?php

namespace AppBundle\Entity;

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
    private $field;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="fieldValues")
     */
    private $client;

    /**
     * Значение поля - текст
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * Значение поля - дата/время
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * Вариант значения (если поле не multiple)
     * @ORM\ManyToOne(targetEntity="ClientFieldOption")
     */
    private $option;

    /**
     * Варианты значений (если поле multiple)
     * @ORM\ManyToMany(targetEntity="ClientFieldOption")
     */
    private $options;

    /**
     * Имя файла для файлового поля
     * @ORM\Column(type="string", nullable=true)
     */
    private $filename;

    /**
     * Значение поля - файл
     * @Vich\UploadableField(mapping="client_field_file", fileNameProperty="filename")
     */
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file = null)
    {
        $this->file = $file;

        if ($file) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * Возвращает значение в зависимости от типа поля
     * @return ClientFieldOption|\DateTime|Collection|null|string
     */
    public function getValue()
    {
        if (!$this->field instanceof ClientField) {
            return null;
        }

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
     * @return ClientFieldOption|\DateTime|Collection|null|string
     */
    public function setValue($value)
    {
        if (!$this->field instanceof ClientField) {
            return null;
        }

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
     * @param string $text
     *
     * @return ClientFieldValue
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return ClientFieldValue
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set filename
     *
     * @param \DateTime $filename
     *
     * @return ClientFieldValue
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return \DateTime
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set field
     *
     * @param \AppBundle\Entity\ClientField $field
     *
     * @return ClientFieldValue
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

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ClientFieldValue
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set option
     *
     * @param \AppBundle\Entity\ClientFieldOption $option
     *
     * @return ClientFieldValue
     */
    public function setOption(ClientFieldOption $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return \AppBundle\Entity\ClientFieldOption
     */
    public function getOption()
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
     * @param \AppBundle\Entity\ClientFieldOption $option
     *
     * @return ClientFieldValue
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
     * @return Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption(ClientFieldOption $option)
    {
        return $this->options->contains($option);
    }

    /**
     * Get options
     *
     * @param Collection $options
     * @return ClientFieldValue
     */
    public function setOptions(Collection $options)
    {
        $this->options = $options;

        return $this;
    }
}
