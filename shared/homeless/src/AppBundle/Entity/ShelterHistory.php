<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Данные о проживании в приюте (договор о заселении)
 * @ORM\Entity()
 */
class ShelterHistory extends BaseEntity
{

    const REASON_FOR_LEAVING_SUPPORTED_ROOM = 1;
    const REASON_FOR_LEAVING_RENT_ROOM = 2;
    const REASON_FOR_LEAVING_VIOLATION = 3;
    const REASON_FOR_LEAVING_HIMSELF = 4;
    const REASON_FOR_LEAVING_DEATH = 5;
    const REASON_FOR_LEAVING_WORK_APARTMENT = 6;
    const REASON_FOR_LEAVING_OTHER_CITY = 7;
    const REASON_FOR_LEAVING_PARENTS = 8;
    const REASON_FOR_LEAVING_DNP_STATE_INSTITUTIONS = 9;
    const REASON_FOR_LEAVING_OUR_SHELTER = 10;
    const REASON_FOR_LEAVING_OTHER_SHELTER = 11;

    public static $resultOfStay = [
       self::REASON_FOR_LEAVING_SUPPORTED_ROOM => 'Съехал в съемную комнату (поддерживаемое проживание)',
       self::REASON_FOR_LEAVING_RENT_ROOM =>  'Съехал в съемную комнату или хостел',
       self::REASON_FOR_LEAVING_VIOLATION => 'Выселен за нарушение правил',
       self::REASON_FOR_LEAVING_HIMSELF => 'Покинул приют по собственному желанию',
       self::REASON_FOR_LEAVING_DEATH => 'Умер',
       self::REASON_FOR_LEAVING_WORK_APARTMENT => 'Съехал на работу с проживанием',
       self::REASON_FOR_LEAVING_OTHER_CITY => 'Уехал в другой город',
       self::REASON_FOR_LEAVING_PARENTS => 'Съехал к родственникам друзьям',
       self::REASON_FOR_LEAVING_DNP_STATE_INSTITUTIONS => 'Съехал в интернат или в гос.стационар для людей с инвалидностью',
       self::REASON_FOR_LEAVING_OUR_SHELTER => 'Съехал в наш приют для пожилых',
       self::REASON_FOR_LEAVING_OTHER_SHELTER => 'Съехал в ДНП или в другие приюты',
    ];

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
     * Контакт клиента записан
     * @ORM\Column(type="smallint")
     */
    private $contact_saved;

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

    /**
     * Причина отбытия
     * @ORM\ManyToOne(targetEntity="ShelterLeavingReason")
     */
    private $leavingReason;

    /**
     * @return mixed
     */
    public function getLeavingReason()
    {
        return $this->leavingReason;
    }

    /**
     * @param mixed $leavingReason
     * @return ShelterHistory
     */
    public function setLeavingReason($leavingReason)
    {
        $this->leavingReason = $leavingReason;
        return $this;
    }


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
     * Set contact_saved
     *
     * @return ShelterHistory
     */
    public function setContactSaved($contact_saved)
    {
        $this->contact_saved = $contact_saved;

        return $this;
    }

    /**
     * Get contact_saved flag
     *
     * @return Boolean
     */
    public function getContactSaved()
    {
        return boolval($this->contact_saved);
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
