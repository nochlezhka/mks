<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Анкета проживающего
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResidentQuestionnaireRepository")
 */
class ResidentQuestionnaire
{
    const TYPE_3 = 1;
    const TYPE_6 = 2;
    const TYPE_12 = 3;

    const ROOM_TYPE_RENTS_A_ROOM = 1;
    const ROOM_TYPE_REMOVES_THE_BED = 2;
    const ROOM_TYPE_RENTS_AN_APARTMENT = 3;
    const ROOM_TYPE_WITH_FRIENDS = 4;
    const ROOM_TYPE_RIGHT_AT_WORK = 5;
    const ROOM_TYPE_CHILD_CARE_CENTER = 6;
    const ROOM_TYPE_DNP_STATE_INSTITUTIONS = 7;
    const ROOM_TYPE_YOUR_HOME = 8;
    const ROOM_TYPE_HOSTEL_FROM_WORK = 9;
    const ROOM_TYPE_OTHER = 10;

    const CHANGED_JOBS_COUNT_0 = 1;
    const CHANGED_JOBS_COUNT_1 = 2;
    const CHANGED_JOBS_COUNT_2 = 3;
    const CHANGED_JOBS_COUNT_3 = 4;

    const REASON_FOR_TRANSITION_BEST_WORKING_CONDITIONS = 1;
    const REASON_FOR_TRANSITION_MORE_INTERESTING_ACTIVITY = 2;
    const REASON_FOR_TRANSITION_CONFLICTS = 3;
    const REASON_FOR_TRANSITION_REDUCTION_OF_WORKPLACE = 4;

    const REASON_FOR_PETITION_HELP_ONLY = 1;
    const REASON_FOR_PETITION_HUMANITARIAN_AID = 2;
    const REASON_FOR_PETITION_ONE_TIME_CONSULTATION = 3;
    const REASON_FOR_PETITION_ESCORT = 4;
    const REASON_FOR_PETITION_RE_SETTLEMENT = 5;

    public static $types = [
        self::TYPE_3 => '3 месяца',
        self::TYPE_6 => '6 месяцев',
        self::TYPE_12 => '1 год',
    ];

    public static $roomTypes = [
        self::ROOM_TYPE_RENTS_A_ROOM => 'Снимает комнату',
        self::ROOM_TYPE_REMOVES_THE_BED => 'Снимает койку',
        self::ROOM_TYPE_RENTS_AN_APARTMENT => 'Снимает квартиру',
        self::ROOM_TYPE_WITH_FRIENDS => 'У знакомых',
        self::ROOM_TYPE_RIGHT_AT_WORK => 'Прямо на работе',
        self::ROOM_TYPE_CHILD_CARE_CENTER => 'Ребцентр',
        self::ROOM_TYPE_DNP_STATE_INSTITUTIONS => 'днп, гос. учреждения',
        self::ROOM_TYPE_YOUR_HOME => 'Своё жилье',
        self::ROOM_TYPE_HOSTEL_FROM_WORK => 'Общежитие от работы',
        self::ROOM_TYPE_OTHER => 'Другое (б-ца, гора, сестра)',
    ];

    public static $changedJobsCounts = [
        self::CHANGED_JOBS_COUNT_0 => 'Не менял',
        self::CHANGED_JOBS_COUNT_1 => '1',
        self::CHANGED_JOBS_COUNT_2 => '2',
        self::CHANGED_JOBS_COUNT_3 => '3 и более',
    ];

    public static $reasonForTransitions = [
        self::REASON_FOR_TRANSITION_BEST_WORKING_CONDITIONS => 'Лучшие условия труда (зарплата, соцпакет, месторасположение и пр.)',
        self::REASON_FOR_TRANSITION_MORE_INTERESTING_ACTIVITY => 'Более интересная деятельность',
        self::REASON_FOR_TRANSITION_CONFLICTS => 'Конфликты',
        self::REASON_FOR_TRANSITION_REDUCTION_OF_WORKPLACE => 'Сокращение рабочего места',
    ];

    public static $reasonForPetition = [
        self::REASON_FOR_PETITION_HELP_ONLY => 'Только справка',
        self::REASON_FOR_PETITION_HUMANITARIAN_AID => 'Гуманитарная помощь',
        self::REASON_FOR_PETITION_ONE_TIME_CONSULTATION => 'Разовая консультация',
        self::REASON_FOR_PETITION_ESCORT => 'Сопровождение',
        self::REASON_FOR_PETITION_RE_SETTLEMENT => 'Повторное заселение',
    ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Тип анкеты проживающего
     * @ORM\Column(type="integer", nullable=true)
     */
    private $typeId = self::TYPE_3;

    /**
     * Проживает в жилом помещении?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDwelling;

    /**
     * Тип жилья
     * @ORM\Column(type="integer", nullable=true)
     */
    private $roomTypeId;

    /**
     * Работает?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWork;

    /**
     * Официальная работа?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWorkOfficial;

    /**
     * Постоянная работа?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWorkConstant;

    /**
     * Сколько сменил работ
     * @ORM\Column(type="integer", nullable=true)
     */
    private $changedJobsCountId;

    /**
     * Причина перехода на другую работу
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasonForTransitionIds;

    /**
     * Причина обращения
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasonForPetitionIds;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documents")
     */
    private $client;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return self
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
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return isset(static::$types[$this->typeId]) ? static::$types[$this->typeId] : $this->typeId;
    }

    /**
     * @param mixed $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getisDwelling()
    {
        return $this->isDwelling;
    }

    /**
     * @param mixed $isDwelling
     */
    public function setIsDwelling($isDwelling)
    {
        $this->isDwelling = $isDwelling;
    }

    /**
     * @return mixed
     */
    public function getRoomTypeId()
    {
        return $this->roomTypeId;
    }

    /**
     * @param mixed $roomTypeId
     */
    public function setRoomTypeId($roomTypeId)
    {
        $this->roomTypeId = $roomTypeId;
    }

    /**
     * @return mixed
     */
    public function getisWork()
    {
        return $this->isWork;
    }

    /**
     * @param mixed $isWork
     */
    public function setIsWork($isWork)
    {
        $this->isWork = $isWork;
    }

    /**
     * @return mixed
     */
    public function getisWorkOfficial()
    {
        return $this->isWorkOfficial;
    }

    /**
     * @param mixed $isWorkOfficial
     */
    public function setIsWorkOfficial($isWorkOfficial)
    {
        $this->isWorkOfficial = $isWorkOfficial;
    }

    /**
     * @return mixed
     */
    public function getisWorkConstant()
    {
        return $this->isWorkConstant;
    }

    /**
     * @param mixed $isWorkConstant
     */
    public function setIsWorkConstant($isWorkConstant)
    {
        $this->isWorkConstant = $isWorkConstant;
    }

    /**
     * @return mixed
     */
    public function getChangedJobsCountId()
    {
        return $this->changedJobsCountId;
    }

    /**
     * @param mixed $changedJobsCountId
     */
    public function setChangedJobsCountId($changedJobsCountId)
    {
        $this->changedJobsCountId = $changedJobsCountId;
    }

    /**
     * @return mixed
     */
    public function getReasonForTransitionIds()
    {
        return explode(',', $this->reasonForTransitionIds);
    }

    /**
     * @param mixed $reasonForTransitionIds
     */
    public function setReasonForTransitionIds($reasonForTransitionIds)
    {
        $this->reasonForTransitionIds = implode(',', $reasonForTransitionIds);
    }

    /**
     * @return mixed
     */
    public function getReasonForPetitionIds()
    {
        return explode(',', $this->reasonForPetitionIds);
    }

    /**
     * @param mixed $reasonForPetitionIds
     */
    public function setReasonForPetitionIds($reasonForPetitionIds)
    {
        $this->reasonForPetitionIds = implode(',', $reasonForPetitionIds);
    }

    /**
     * Заполнено менеджером?
     *
     * @return bool
     */
    public function isFull()
    {
        return $this->isDwelling !== null ||
            $this->roomTypeId !== null ||
            $this->isWork !== null ||
            $this->isWorkOfficial !== null ||
            $this->isWorkConstant !== null ||
            $this->changedJobsCountId !== null ||
            $this->reasonForTransitionIds !== null ||
            $this->reasonForPetitionIds !== null;
    }
}
