<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Service\DownloadableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Договор (сервисный план)
 */
#[ORM\Entity]
class Contract extends BaseEntity implements DownloadableInterface
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateFrom = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateTo = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'contracts')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: ContractStatus::class)]
    private ?ContractStatus $status = null;

    #[ORM\ManyToOne(targetEntity: Document::class)]
    private ?Document $document = null;

    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: ContractItem::class, cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'DESC', 'id' => 'DESC'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }

    public function __set($name, $value): void {}

    public function getNamePrefix(): string
    {
        return 'contract';
    }

    public function getLabel(): string
    {
        $label = $this->getNumber();

        if ($this->getDateFrom() instanceof \DateTimeImmutable) {
            $label .= ' от '.$this->getDateFrom()->format('d.m.Y');
        }

        if ($this->getDateTo() instanceof \DateTimeImmutable) {
            $label .= ' до '.$this->getDateTo()->format('d.m.Y');
        }

        if ($this->getStatus() instanceof ContractStatus) {
            $label .= ' ('.$this->getStatus().')';
        }

        if ($this->getCreatedBy() instanceof User) {
            $label .= ', '.$this->getCreatedBy();
        }

        return $label;
    }

    /**
     * Срок действия договора в месяцах
     */
    public function getDuration(): ?int
    {
        $dateTo = $this->dateTo;
        if ($dateTo === null) {
            return null;
        }

        return $this->dateFrom->diff($dateTo)->m;
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeImmutable $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeImmutable $dateTo): self
    {
        $this->dateTo = $dateTo;

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

    public function getStatus(): ?ContractStatus
    {
        return $this->status;
    }

    public function setStatus(?ContractStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ContractItem $item): self
    {
        $item->setContract($this);
        $this->items->add($item);

        return $this;
    }

    public function removeItem(ContractItem $item): void
    {
        $item->setContract(null);
        $this->items->removeElement($item);
    }

    #[Assert\Callback]
    public function validate(ExecutionContext $context): void
    {
        if ($this->items->isEmpty()) {
            $context->addViolation('Не указан пункт', ['items']);
        }
    }
}
