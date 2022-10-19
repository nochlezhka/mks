<?php

namespace AppBundle\Entity;

use DateTime;
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
    private ?string $comment = null;

    /**
     * Дата прививки от дифтерии
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $diphtheriaVaccinationDate = null;

    /**
     * Дата флюорографии
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $fluorographyDate = null;

    /**
     * Дата прививки от гепатита
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $hepatitisVaccinationDate = null;

    /**
     * Дата прививки от тифа
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $typhusVaccinationDate = null;

    /**
     * Дата заселения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateFrom = null;

    /**
     * Дата выселения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateTo = null;

    /**
     * Комната
     * @ORM\ManyToOne(targetEntity="ShelterRoom")
     */
    private ?ShelterRoom $room = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="shelterHistories")
     */
    private ?Client $client = null;

    /**
     * Статус
     * @ORM\ManyToOne(targetEntity="ShelterStatus")
     */
    private ?ShelterStatus $status = null;

    /**
     * Договор
     * @ORM\ManyToOne(targetEntity="Contract")
     */
    private ?Contract $contract = null;

    public function __toString()
    {
        $status = $this->getStatus();
        return $status->getName();
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return ShelterHistory
     */
    public function setComment(?string $comment): ShelterHistory
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

    /**
     * Set diphtheriaVaccinationDate
     *
     * @param DateTime|null $diphtheriaVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setDiphtheriaVaccinationDate(?DateTime $diphtheriaVaccinationDate): ShelterHistory
    {
        $this->diphtheriaVaccinationDate = $diphtheriaVaccinationDate;

        return $this;
    }

    /**
     * Get diphtheriaVaccinationDate
     *
     * @return DateTime
     */
    public function getDiphtheriaVaccinationDate(): ?DateTime
    {
        return $this->diphtheriaVaccinationDate;
    }

    /**
     * Set fluorographyDate
     *
     * @param DateTime|null $fluorographyDate
     *
     * @return ShelterHistory
     */
    public function setFluorographyDate(?DateTime $fluorographyDate): ShelterHistory
    {
        $this->fluorographyDate = $fluorographyDate;

        return $this;
    }

    /**
     * Get fluorographyDate
     *
     * @return DateTime
     */
    public function getFluorographyDate(): ?DateTime
    {
        return $this->fluorographyDate;
    }

    /**
     * Set hepatitisVaccinationDate
     *
     * @param DateTime|null $hepatitisVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setHepatitisVaccinationDate(?DateTime $hepatitisVaccinationDate): ShelterHistory
    {
        $this->hepatitisVaccinationDate = $hepatitisVaccinationDate;

        return $this;
    }

    /**
     * Get hepatitisVaccinationDate
     *
     * @return DateTime
     */
    public function getHepatitisVaccinationDate(): ?DateTime
    {
        return $this->hepatitisVaccinationDate;
    }

    /**
     * Set typhusVaccinationDate
     *
     * @param DateTime|null $typhusVaccinationDate
     *
     * @return ShelterHistory
     */
    public function setTyphusVaccinationDate(?DateTime $typhusVaccinationDate): ShelterHistory
    {
        $this->typhusVaccinationDate = $typhusVaccinationDate;

        return $this;
    }

    /**
     * Get typhusVaccinationDate
     *
     * @return DateTime
     */
    public function getTyphusVaccinationDate(): ?DateTime
    {
        return $this->typhusVaccinationDate;
    }

    /**
     * Set dateFrom
     *
     * @param DateTime|null $dateFrom
     *
     * @return ShelterHistory
     */
    public function setDateFrom(?DateTime $dateFrom): ShelterHistory
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return DateTime
     */
    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param DateTime $dateTo
     *
     * @return ShelterHistory
     */
    public function setDateTo(?DateTime $dateTo): ShelterHistory
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return DateTime
     */
    public function getDateTo(): ?DateTime
    {
        return $this->dateTo;
    }

    /**
     * Set room
     *
     * @param ShelterRoom|null $room
     *
     * @return ShelterHistory
     */
    public function setRoom(ShelterRoom $room): ShelterHistory
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return ShelterRoom
     */
    public function getRoom(): ?ShelterRoom
    {
        return $this->room;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return ShelterHistory
     */
    public function setClient(Client $client): ShelterHistory
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Set status
     *
     * @param ShelterStatus|null $status
     *
     * @return ShelterHistory
     */
    public function setStatus(ShelterStatus $status): ShelterHistory
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return ShelterStatus
     */
    public function getStatus(): ?ShelterStatus
    {
        return $this->status;
    }

    /**
     * Set contract
     *
     * @param Contract|null $contract
     *
     * @return ShelterHistory
     */
    public function setContract(Contract $contract): ShelterHistory
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Get contract
     *
     * @return Contract
     */
    public function getContract(): ?Contract
    {
        return $this->contract;
    }
}
