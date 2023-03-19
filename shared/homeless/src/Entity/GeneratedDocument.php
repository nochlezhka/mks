<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Service\DownloadableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Построенный документ
 */
#[ORM\Entity]
class GeneratedDocument extends BaseEntity implements DownloadableInterface
{
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'generatedDocuments')]
    private ?Client $client = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $number = null;

    #[ORM\ManyToOne(targetEntity: GeneratedDocumentType::class)]
    private ?GeneratedDocumentType $type = null;

    #[ORM\ManyToOne(targetEntity: GeneratedDocumentStartText::class)]
    private ?GeneratedDocumentStartText $startText = null;

    #[ORM\ManyToOne(targetEntity: GeneratedDocumentEndText::class)]
    private ?GeneratedDocumentEndText $endText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $whom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $signature = null;

    public function __toString(): string
    {
        return $this->type->getName() ?? '';
    }

    public function getNamePrefix(): string
    {
        return 'generated-document';
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
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

    public function getWhom(): ?string
    {
        return $this->whom;
    }

    public function setWhom(?string $whom): self
    {
        $this->whom = $whom;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function getType(): ?GeneratedDocumentType
    {
        return $this->type;
    }

    public function setType(GeneratedDocumentType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStartText(): ?GeneratedDocumentStartText
    {
        return $this->startText;
    }

    public function setStartText(GeneratedDocumentStartText $startText): self
    {
        $this->startText = $startText;

        return $this;
    }

    public function getEndText(): ?GeneratedDocumentEndText
    {
        return $this->endText;
    }

    public function setEndText(GeneratedDocumentEndText $endText): self
    {
        $this->endText = $endText;

        return $this;
    }
}
