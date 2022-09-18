<?php

namespace AppBundle\Entity;

use DateTime;
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
    private ?string $comment;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documentFiles")
     */
    private Client $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="DocumentType")
     */
    private DocumentType $type;

    /**
     * Имя файла
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $filename;

    /**
     * Файл
     * @Vich\UploadableField(mapping="document_file", fileNameProperty="filename")
     */
    private $file;

    public function __toString()
    {
        $type = $this->getType();

        return $type->getName();
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file = null): DocumentFile
    {
        $this->file = $file;

        if ($file) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * Set filename
     *
     * @param string|null $filename
     *
     * @return DocumentFile
     */
    public function setFilename(?string $filename): DocumentFile
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return DocumentFile
     */
    public function setComment(?string $comment): DocumentFile
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return DocumentFile
     */
    public function setClient(Client $client = null): DocumentFile
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


    /**
     * Set type
     *
     * @param DocumentType|null $type
     *
     * @return DocumentFile
     */
    public function setType(DocumentType $type = null): DocumentFile
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return DocumentType
     */
    public function getType(): DocumentType
    {
        return $this->type;
    }
}
