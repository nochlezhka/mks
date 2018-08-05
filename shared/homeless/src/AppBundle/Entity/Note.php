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
    private $text;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="notes")
     */
    private $client;

    /**
     * Важное
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $important = false;

    public function __toString()
    {
        return (string)mb_substr(strip_tags($this->text), 0, 100);
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Note
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
     * Set important
     *
     * @param boolean $important
     *
     * @return Note
     */
    public function setImportant($important)
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important
     *
     * @return boolean
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Note
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
}
