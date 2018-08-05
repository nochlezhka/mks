<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Комната приюта
 * @ORM\Entity()
 */
class ShelterRoom extends BaseEntity
{
    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * Максимальное количество жильцов
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxOccupants;

    /**
     * Текущее количество жильцов
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentOccupants;

    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function __toString()
    {
        return (string)$this->number;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return ShelterRoom
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set maxOccupants
     *
     * @param integer $maxOccupants
     *
     * @return ShelterRoom
     */
    public function setMaxOccupants($maxOccupants)
    {
        $this->maxOccupants = $maxOccupants;

        return $this;
    }

    /**
     * Get maxOccupants
     *
     * @return integer
     */
    public function getMaxOccupants()
    {
        return $this->maxOccupants;
    }

    /**
     * Set currentOccupants
     *
     * @param integer $currentOccupants
     *
     * @return ShelterRoom
     */
    public function setCurrentOccupants($currentOccupants)
    {
        $this->currentOccupants = $currentOccupants;

        return $this;
    }

    /**
     * Get currentOccupants
     *
     * @return integer
     */
    public function getCurrentOccupants()
    {
        return $this->currentOccupants;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return ShelterRoom
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
