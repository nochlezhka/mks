<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Repository\ResidentQuestionnaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Анкета проживающего
 */
#[ORM\Entity(repositoryClass: ResidentQuestionnaireRepository::class)]
class ResidentQuestionnaire
{
    public const TYPE_3 = 1;
    public const TYPE_6 = 2;
    public const TYPE_12 = 3;

    public const ROOM_TYPE_RENTS_A_ROOM = 1;
    public const ROOM_TYPE_REMOVES_THE_BED = 2;
    public const ROOM_TYPE_RENTS_AN_APARTMENT = 3;
    public const ROOM_TYPE_WITH_FRIENDS = 4;
    public const ROOM_TYPE_RIGHT_AT_WORK = 5;
    public const ROOM_TYPE_CHILD_CARE_CENTER = 6;
    public const ROOM_TYPE_DNP_STATE_INSTITUTIONS = 7;
    public const ROOM_TYPE_YOUR_HOME = 8;
    public const ROOM_TYPE_HOSTEL_FROM_WORK = 9;
    public const ROOM_TYPE_OTHER = 10;

    public const CHANGED_JOBS_COUNT_0 = 1;
    public const CHANGED_JOBS_COUNT_1 = 2;
    public const CHANGED_JOBS_COUNT_2 = 3;
    public const CHANGED_JOBS_COUNT_3 = 4;

    public const REASON_FOR_TRANSITION_BEST_WORKING_CONDITIONS = 1;
    public const REASON_FOR_TRANSITION_MORE_INTERESTING_ACTIVITY = 2;
    public const REASON_FOR_TRANSITION_CONFLICTS = 3;
    public const REASON_FOR_TRANSITION_REDUCTION_OF_WORKPLACE = 4;

    public const REASON_FOR_PETITION_HELP_ONLY = 1;
    public const REASON_FOR_PETITION_HUMANITARIAN_AID = 2;
    public const REASON_FOR_PETITION_ONE_TIME_CONSULTATION = 3;
    public const REASON_FOR_PETITION_ESCORT = 4;
    public const REASON_FOR_PETITION_RE_SETTLEMENT = 5;

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

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /**
     * Тип анкеты проживающего
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $typeId = self::TYPE_3;

    /**
     * Проживает в жилом помещении?
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isDwelling = false;

    /**
     * Тип жилья
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $roomTypeId = null;

    /**
     * Работает?
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isWork = false;

    /**
     * Официальная работа?
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isWorkOfficial = false;

    /**
     * Постоянная работа?
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isWorkConstant = false;

    /**
     * Сколько сменил работ
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $changedJobsCountId = null;

    /**
     * Причина перехода на другую работу
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reasonForTransitionIds = null;

    /**
     * Причина обращения
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reasonForPetitionIds = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function getType(): string
    {
        return array_search($this->typeId, static::$types, true) ?: (string) $this->typeId;
    }

    public function setTypeId(?int $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function isDwelling(): bool
    {
        return $this->isDwelling;
    }

    public function setIsDwelling(bool $isDwelling): void
    {
        $this->isDwelling = $isDwelling;
    }

    public function getRoomTypeId(): ?int
    {
        return $this->roomTypeId;
    }

    public function setRoomTypeId(?int $roomTypeId): void
    {
        $this->roomTypeId = $roomTypeId;
    }

    public function isWork(): bool
    {
        return $this->isWork;
    }

    public function setIsWork(bool $isWork): void
    {
        $this->isWork = $isWork;
    }

    public function isWorkOfficial(): bool
    {
        return $this->isWorkOfficial;
    }

    public function setIsWorkOfficial(bool $isWorkOfficial): void
    {
        $this->isWorkOfficial = $isWorkOfficial;
    }

    public function isWorkConstant(): bool
    {
        return $this->isWorkConstant;
    }

    public function setIsWorkConstant(bool $isWorkConstant): void
    {
        $this->isWorkConstant = $isWorkConstant;
    }

    public function getChangedJobsCountId(): ?int
    {
        return $this->changedJobsCountId;
    }

    public function setChangedJobsCountId(?int $changedJobsCountId): void
    {
        $this->changedJobsCountId = $changedJobsCountId;
    }

    /**
     * @return array<string>
     */
    public function getReasonForTransitionIds(): array
    {
        return explode(',', $this->reasonForTransitionIds);
    }

    /**
     * @param array<string> $reasonForTransitionIds
     */
    public function setReasonForTransitionIds(array $reasonForTransitionIds): void
    {
        $this->reasonForTransitionIds = implode(',', $reasonForTransitionIds);
    }

    /**
     * @return array<string>
     */
    public function getReasonForPetitionIds(): array
    {
        return explode(',', $this->reasonForPetitionIds);
    }

    /**
     * @param array<string> $reasonForPetitionIds
     */
    public function setReasonForPetitionIds(array $reasonForPetitionIds): void
    {
        $this->reasonForPetitionIds = implode(',', $reasonForPetitionIds);
    }

    /**
     * Заполнено менеджером?
     */
    public function isFull(): bool
    {
        return $this->isDwelling !== null
            || $this->roomTypeId !== null
            || $this->isWork !== null
            || $this->isWorkOfficial !== null
            || $this->isWorkConstant !== null
            || $this->changedJobsCountId !== null
            || $this->reasonForTransitionIds !== null
            || $this->reasonForPetitionIds !== null;
    }
}
