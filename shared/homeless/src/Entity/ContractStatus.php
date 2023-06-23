<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Статус договора
 */
#[ORM\Entity]
class ContractStatus extends BaseEntity
{
    /**
     * SyncId статуса "В процессе выполнения"
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
