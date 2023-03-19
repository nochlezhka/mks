<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Статус проживания в приюте
 */
#[ORM\Entity]
class ShelterStatus extends BaseEntity
{
    /**
     * SyncId статуса "проживает"
     */
    public const IN_PROCESS = 1;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
