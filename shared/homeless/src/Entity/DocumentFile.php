<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Загруженный файл документа
 */
#[ORM\Entity]
#[Vich\Uploadable]
class DocumentFile extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'documentFiles')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: DocumentType::class)]
    private ?DocumentType $type = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $filename = null;

    #[Vich\UploadableField(mapping: 'document_file', fileNameProperty: 'filename')]
    private ?File $file = null;

    public function __toString(): string
    {
        return $this->getType()?->getName() ?? 'UNKNOWN_TYPE';
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile($file = null): self
    {
        $this->file = $file;

        if ($file) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?DocumentType
    {
        return $this->type;
    }

    public function setType(DocumentType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
