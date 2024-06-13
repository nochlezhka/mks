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
    public const int IN_PROCESS = 1;
    /**
     * SyncId статуса "Не выполнен по причине отказа клиента"
     */
    public const int REJECTED_CLIENT_REFUSAL = 5;
    /**
     * SyncId статуса "Не выполнен по другим причинам"
     */
    public const int REJECTED_OTHER = 6;
    /**
     * SyncId статуса "Не выполнен по причине неявки клиента"
     */
    public const int REJECTED_CLIENT_NON_APPEARANCE = 8;

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
