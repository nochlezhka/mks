<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Загруженный файл документа
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class DocumentFile extends BaseEntity
{
    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documentFiles")
     */
    private $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="DocumentType")
     */
    private $type;

    /**
     * Имя файла
     * @ORM\Column(type="string", nullable=true)
     */
    private $filename;

    /**
     * Файл
     * @Vich\UploadableField(mapping="document_file", fileNameProperty="filename")
     */
    private $file;

    public function __toString()
    {
        $type = $this->getType();

        if ($type instanceof DocumentType) {
            return (string)$type->getName();
        }

        return '';
    }

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
     * Set filename
     *
     * @param \DateTime $filename
     *
     * @return DocumentFile
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
     * Set comment
     *
     * @param string $comment
     *
     * @return DocumentFile
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return DocumentFile
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
     * Set type
     *
     * @param \AppBundle\Entity\DocumentType $type
     *
     * @return DocumentFile
     */
    public function setType(DocumentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\DocumentType
     */
    public function getType()
    {
        return $this->type;
    }
}
