<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Примечание
 * @ORM\Entity()
 */
class Note extends BaseEntity
{
    /**
     * Текст
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="notes")
     */
    private Client $client;

    /**
     * Важное
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $important = false;

    public function __toString()
    {
        return mb_substr(strip_tags($this->text), 0, 100);
    }

    /**
     * Set text
     *
     * @param string|null $text
     *
     * @return Note
     */
    public function setText(?string $text): Note
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
     * Set important
     *
     * @param boolean $important
     *
     * @return Note
     */
    public function setImportant(?bool $important): Note
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important
     *
     * @return boolean
     */
    public function getImportant(): ?bool
    {
        return $this->important;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Note
     */
    public function setClient(Client $client = null): Note
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
