<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Комната приюта
 */
#[ORM\Entity]
class ShelterRoom extends BaseEntity
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $number = null;

    /**
     * Максимальное количество жильцов
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxOccupants = null;

    /**
     * Текущее количество жильцов
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $currentOccupants = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    public function __toString(): string
    {
        return $this->number ?? '';
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

    public function getMaxOccupants(): ?int
    {
        return $this->maxOccupants;
    }

    public function setMaxOccupants(?int $maxOccupants): self
    {
        $this->maxOccupants = $maxOccupants;

        return $this;
    }

    public function getCurrentOccupants(): ?int
    {
        return $this->currentOccupants;
    }

    public function setCurrentOccupants(?int $currentOccupants): self
    {
        $this->currentOccupants = $currentOccupants;

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
}
