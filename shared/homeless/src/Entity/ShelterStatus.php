<?php

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
    const IN_PROCESS = 1;

    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ShelterStatus
     */
    public function setName(?string $name): ShelterStatus
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
