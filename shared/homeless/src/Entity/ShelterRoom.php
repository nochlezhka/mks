<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Комната приюта
 */
#[ORM\Entity]
class ShelterRoom extends BaseEntity
{
    /**
     * Номер
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $number = null;

    /**
     * Максимальное количество жильцов
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxOccupants = null;

    /**
     * Текущее количество жильцов
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $currentOccupants = null;

    /**
     * Комментарий
     */
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $comment = null;

    public function __toString()
    {
        return $this->number;
    }

    /**
     * Set number
     *
     * @param string|null $number
     *
     * @return ShelterRoom
     */
    public function setNumber(?string $number): ShelterRoom
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Set maxOccupants
     *
     * @param int|null $maxOccupants
     *
     * @return ShelterRoom
     */
    public function setMaxOccupants(?int $maxOccupants): ShelterRoom
    {
        $this->maxOccupants = $maxOccupants;

        return $this;
    }

    /**
     * Get maxOccupants
     *
     * @return integer
     */
    public function getMaxOccupants(): ?int
    {
        return $this->maxOccupants;
    }

    /**
     * Set currentOccupants
     *
     * @param int|null $currentOccupants
     *
     * @return ShelterRoom
     */
    public function setCurrentOccupants(?int $currentOccupants): ShelterRoom
    {
        $this->currentOccupants = $currentOccupants;

        return $this;
    }

    /**
     * Get currentOccupants
     *
     * @return integer
     */
    public function getCurrentOccupants(): ?int
    {
        return $this->currentOccupants;
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return ShelterRoom
     */
    public function setComment(?string $comment): ShelterRoom
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
