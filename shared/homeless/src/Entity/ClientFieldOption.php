<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Вариант значения дополнительного поля клиента
 */
#[ORM\Entity]
class ClientFieldOption extends BaseEntity
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    /**
     * Если - true, то при заполнении не может быть выбрано только
     * одно это значение, необходимо указать еще какое-нибудь
     */
    #[ORM\Column(type: 'boolean')]
    private bool $notSingle = false;

    #[ORM\ManyToOne(targetEntity: ClientField::class, inversedBy: 'options')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ClientField $field = null;

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

    public function isNotSingle(): bool
    {
        return $this->notSingle;
    }

    public function setNotSingle(bool $notSingle): self
    {
        $this->notSingle = $notSingle;

        return $this;
    }

    public function getField(): ?ClientField
    {
        return $this->field;
    }

    public function setField(?ClientField $field): self
    {
        $this->field = $field;

        return $this;
    }
}
