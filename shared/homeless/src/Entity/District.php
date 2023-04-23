<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Район региона РФ
 */
#[ORM\Entity]
class District extends BaseEntity
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'districts')]
    private ?Region $region = null;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;

        return $this;
    }
}
