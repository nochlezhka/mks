<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Данные о проживании в приюте (договор о заселении)
 * @ORM\Entity()
 */
class ShelterHistory extends BaseEntity
{
    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Дата прививки от дифтерии
     * @ORM\Column(type="date", nullable=true)
     */
    private $diphtheriaVaccinationDate;

    /**
     * Дата флюорографии
     * @ORM\Column(type="date", nullable=true)
     */
    private $fluorographyDate;

    /**
     * Дата прививки от гепатита
     * @ORM\Column(type="date", nullable=true)
     */
    private $hepatitisVaccinationDate;

    /**
     * Дата прививки от тифа
     * @ORM\Column(type="date", nullable=true)
     */
    private $typhusVaccinationDate;

    /**
     * Дата заселения
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFrom;

    /**
     * Дата выселения
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateTo;

    /**
     * Комната
     * @ORM\ManyToOne(targetEntity="ShelterRoom")
     */
    private $room;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="shelterHistories")
     */
    private $client;

    /**
     * Статус
     * @ORM\ManyToOne(targetEntity="ShelterStatus")
     */
    private $status;

    /**
     * Договор
     * @ORM\ManyToOne(targetEntity="Contract")
     */
    private $contract;

    public function __toString()
    {
        $status = $this->getStatus();

        if ($status instanceof ShelterStatus) {
            return (string)$status->getName();
        }

        return '';
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return ShelterHistory
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

    /**
     * Set diphtheriaVaccinationDate
     *
     * @param \DateTime $diphtheriaVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setDiphtheriaVaccinationDate($diphtheriaVaccinationDate)
    {
        $this->diphtheriaVaccinationDate = $diphtheriaVaccinationDate;

        return $this;
    }

    /**
     * Get diphtheriaVaccinationDate
     *
     * @return \DateTime
     */
    public function getDiphtheriaVaccinationDate()
    {
        return $this->diphtheriaVaccinationDate;
    }

    /**
     * Set fluorographyDate
     *
     * @param \DateTime $fluorographyDate
     *
     * @return ShelterHistory
     */
    public function setFluorographyDate($fluorographyDate)
    {
        $this->fluorographyDate = $fluorographyDate;

        return $this;
    }

    /**
     * Get fluorographyDate
     *
     * @return \DateTime
     */
    public function getFluorographyDate()
    {
        return $this->fluorographyDate;
    }

    /**
     * Set hepatitisVaccinationDate
     *
     * @param \DateTime $hepatitisVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setHepatitisVaccinationDate($hepatitisVaccinationDate)
    {
        $this->hepatitisVaccinationDate = $hepatitisVaccinationDate;

        return $this;
    }

    /**
     * Get hepatitisVaccinationDate
     *
     * @return \DateTime
     */
    public function getHepatitisVaccinationDate()
    {
        return $this->hepatitisVaccinationDate;
    }

    /**
     * Set typhusVaccinationDate
     *
     * @param \DateTime $typhusVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setTyphusVaccinationDate($typhusVaccinationDate)
    {
        $this->typhusVaccinationDate = $typhusVaccinationDate;

        return $this;
    }

    /**
     * Get typhusVaccinationDate
     *
     * @return \DateTime
     */
    public function getTyphusVaccinationDate()
    {
        return $this->typhusVaccinationDate;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return ShelterHistory
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return ShelterHistory
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set room
     *
     * @param \AppBundle\Entity\ShelterRoom $room
     *
     * @return ShelterHistory
     */
    public function setRoom(ShelterRoom $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \AppBundle\Entity\ShelterRoom
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ShelterHistory
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set status
     *
     * @param \AppBundle\Entity\ShelterStatus $status
     *
     * @return ShelterHistory
     */
    public function setStatus(ShelterStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\ShelterStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set contract
     *
     * @param \AppBundle\Entity\Contract $contract
     *
     * @return ShelterHistory
     */
    public function setContract(Contract $contract = null)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Get contract
     *
     * @return \AppBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }
}
