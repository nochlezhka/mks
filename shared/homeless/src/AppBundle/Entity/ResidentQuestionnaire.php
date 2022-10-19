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

    public static array $types = [
        '3 месяца' => self::TYPE_3,
        '6 месяцев' => self::TYPE_6,
        '1 год' => self::TYPE_12,
    ];

    public static array $roomTypes = [
        'Снимает комнату' => self::ROOM_TYPE_RENTS_A_ROOM,
        'Снимает койку' => self::ROOM_TYPE_REMOVES_THE_BED,
        'Снимает квартиру' => self::ROOM_TYPE_RENTS_AN_APARTMENT,
        'У знакомых' => self::ROOM_TYPE_WITH_FRIENDS,
        'Прямо на работе' => self::ROOM_TYPE_RIGHT_AT_WORK,
        'Ребцентр' => self::ROOM_TYPE_CHILD_CARE_CENTER,
        'днп, гос. учреждения' => self::ROOM_TYPE_DNP_STATE_INSTITUTIONS,
        'Своё жилье' => self::ROOM_TYPE_YOUR_HOME,
        'Общежитие от работы' => self::ROOM_TYPE_HOSTEL_FROM_WORK,
        'Другое (б-ца, гора, сестра)' => self::ROOM_TYPE_OTHER,
    ];

    public static array $changedJobsCounts = [
        'Не менял' => self::CHANGED_JOBS_COUNT_0,
        '1' => self::CHANGED_JOBS_COUNT_1,
        '2' => self::CHANGED_JOBS_COUNT_2,
        '3 и более' => self::CHANGED_JOBS_COUNT_3,
    ];

    public static array $reasonForTransitions = [
        'Лучшие условия труда (зарплата, соцпакет, месторасположение и пр.)' => self::REASON_FOR_TRANSITION_BEST_WORKING_CONDITIONS,
        'Более интересная деятельность' => self::REASON_FOR_TRANSITION_MORE_INTERESTING_ACTIVITY,
        'Конфликты' => self::REASON_FOR_TRANSITION_CONFLICTS,
        'Сокращение рабочего места' => self::REASON_FOR_TRANSITION_REDUCTION_OF_WORKPLACE,
    ];

    public static array $reasonForPetition = [
        'Только справка' => self::REASON_FOR_PETITION_HELP_ONLY,
        'Гуманитарная помощь' => self::REASON_FOR_PETITION_HUMANITARIAN_AID,
        'Разовая консультация' => self::REASON_FOR_PETITION_ONE_TIME_CONSULTATION,
        'Сопровождение' => self::REASON_FOR_PETITION_ESCORT,
        'Повторное заселение' => self::REASON_FOR_PETITION_RE_SETTLEMENT,
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
    private ?int $typeId = self::TYPE_3;

    /**
     * Проживает в жилом помещении?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isDwelling = null;

    /**
     * Тип жилья
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $roomTypeId = null;

    /**
     * Работает?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWork = null;

    /**
     * Официальная работа?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWorkOfficial = null;

    /**
     * Постоянная работа?
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWorkConstant = null;

    /**
     * Сколько сменил работ
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $changedJobsCountId = null;

    /**
     * Причина перехода на другую работу
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $reasonForTransitionIds = null;

    /**
     * Причина обращения
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $reasonForPetitionIds = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="documents")
     */
    private ?Client $client = null;

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
     * @param Client|null $client
     *
     * @return self
     */
    public function setClient(Client $client): ResidentQuestionnaire
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
     * @return int
     */
    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return static::$types[$this->typeId] ?? $this->typeId;
    }

    /**
     * @param int|null $typeId
     */
    public function setTypeId(?int $typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return bool|null
     */
    public function getisDwelling(): ?bool
    {
        return $this->isDwelling;
    }

    /**
     * @param bool|null $isDwelling
     */
    public function setIsDwelling(?bool $isDwelling)
    {
        $this->isDwelling = $isDwelling;
    }

    /**
     * @return int|null
     */
    public function getRoomTypeId(): ?int
    {
        return $this->roomTypeId;
    }

    /**
     * @param int|null $roomTypeId
     */
    public function setRoomTypeId(?int $roomTypeId)
    {
        $this->roomTypeId = $roomTypeId;
    }

    /**
     * @return bool|null
     */
    public function getisWork(): ?bool
    {
        return $this->isWork;
    }

    /**
     * @param bool|null $isWork
     */
    public function setIsWork(?bool $isWork)
    {
        $this->isWork = $isWork;
    }

    /**
     * @return bool|null
     */
    public function getisWorkOfficial(): ?bool
    {
        return $this->isWorkOfficial;
    }

    /**
     * @param bool|null $isWorkOfficial
     */
    public function setIsWorkOfficial(?bool $isWorkOfficial)
    {
        $this->isWorkOfficial = $isWorkOfficial;
    }

    /**
     * @return bool|null
     */
    public function getisWorkConstant(): ?bool
    {
        return $this->isWorkConstant;
    }

    /**
     * @param bool|null $isWorkConstant
     */
    public function setIsWorkConstant(?bool $isWorkConstant)
    {
        $this->isWorkConstant = $isWorkConstant;
    }

    /**
     * @return int|null
     */
    public function getChangedJobsCountId(): ?int
    {
        return $this->changedJobsCountId;
    }

    /**
     * @param int|null $changedJobsCountId
     */
    public function setChangedJobsCountId(?int $changedJobsCountId)
    {
        $this->changedJobsCountId = $changedJobsCountId;
    }

    /**
     * @return false|string[]
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
     * @return false|string[]
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
    public function isFull(): bool
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
