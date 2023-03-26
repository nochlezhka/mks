<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Repository\ServiceTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Тип услуги
 */
#[ORM\Entity(repositoryClass: ServiceTypeRepository::class)]
class ServiceType extends BaseEntity
{
    // SyncId различных типов
    // Консультация
    public const CONSULTATION = 1;
    // Продукты
    public const PRODUCTS = 2;
    // Комплект одежды
    public const SET_OF_CLOTHES = 3;
    // Получена корреспонденция
    public const CORRESPONDENCE_RECEIVED = 4;
    // Передана корреспонденция
    public const CORRESPONDENCE_TRANSMITTED = 5;
    // Содействие в получении медпомощи
    public const ASSISTANCE_IN_OBTAINING_MEDICAL_CARE = 6;
    // Направление в диспансер
    public const REFERRAL_DISPENSARY = 10;
    // Изготовление фотографий
    public const MAKING_PHOTOS = 12;
    // Написание заявлений/запросов
    public const WRITING_REQUESTS = 13;
    // Оплата проезда
    public const PAYMENT_TRAVEL = 14;
    // Консультация психолога первичная
    public const PRIMARY_PSYCHOLOGICAL_COUNSELING = 15;
    // Оплата пошлины
    public const DUTY_PAYMENT = 16;
    // Средства гигиены
    public const MEANS_HYGIENE = 17;
    // Консультация психолога повторная
    public const REPEATED_COUNSELING_PSYCHOLOGIST = 20;
    // письмо вручено
    public const LETTER_WAS_GIVEN = 21;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    /**
     * Платная
     */
    #[ORM\Column(type: 'boolean')]
    private bool $pay = false;

    #[ORM\Column(type: 'boolean')]
    private bool $document = false;

    /**
     * Сумма
     */
    #[ORM\Column(type: 'boolean')]
    private bool $amount = false;

    #[ORM\Column(type: 'boolean')]
    private bool $comment = false;

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

    public function isPay(): bool
    {
        return $this->pay;
    }

    public function setPay(bool $pay): self
    {
        $this->pay = $pay;

        return $this;
    }

    public function isDocument(): bool
    {
        return $this->document;
    }

    public function setDocument(bool $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function isAmount(): bool
    {
        return $this->amount;
    }

    public function setAmount(bool $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isComment(): bool
    {
        return $this->comment;
    }

    public function setComment(bool $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
