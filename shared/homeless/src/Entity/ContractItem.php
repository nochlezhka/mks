<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Пункт договора (сервисного плана)
 */
#[ORM\Entity]
class ContractItem extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateStart = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(targetEntity: Contract::class, inversedBy: 'items')]
    private ?Contract $contract = null;

    #[ORM\ManyToOne(targetEntity: ContractItemType::class)]
    private ?ContractItemType $type;

    public function __toString(): string
    {
        $type = $this->getType();

        if ($type instanceof ContractItemType) {
            return $type->getName() ?? '';
        }

        return '';
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getType(): ?ContractItemType
    {
        return $this->type;
    }

    public function setType(?ContractItemType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeImmutable $dateStart): void
    {
        $this->dateStart = $dateStart;
    }
}
