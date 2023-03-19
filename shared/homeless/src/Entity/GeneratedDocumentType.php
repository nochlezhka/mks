<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип построенного документа
 */
#[ORM\Entity]
class GeneratedDocumentType extends BaseEntity
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $code = null;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
