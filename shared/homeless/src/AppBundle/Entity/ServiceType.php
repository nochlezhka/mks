<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип услуги
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceTypeRepository")
 */
class ServiceType extends BaseEntity
{
    // SyncId различных типов
    //Консультация
    const CONSULTATION = 1;
    //Продукты
    const PRODUCTS = 2;
    //Комплект одежды
    const SET_OF_CLOTHES = 3;
    //Получена корреспонденция
    const CORRESPONDENCE_RECEIVED = 4;
    //Передана корреспонденция
    const CORRESPONDENCE_TRANSMITTED = 5;
    //Содействие в получении медпомощи
    const ASSISTANCE_IN_OBTAINING_MEDICAL_CARE = 6;
    //Направление в диспансер
    const REFERRAL_DISPENSARY = 10;
    //Изготовление фотографий
    const MAKING_PHOTOS = 12;
    //Написание заявлений/запросов
    const WRITING_REQUESTS = 13;
    //Оплата проезда
    const PAYMENT_TRAVEL = 14;
    //Консультация психолога первичная
    const PRIMARY_PSYCHOLOGICAL_COUNSELING = 15;
    //Оплата пошлины
    const DUTY_PAYMENT = 16;
    //Средства гигиены
    const MEANS_HYGIENE = 17;
    //Консультация психолога повторная
    const REPEATED_COUNSELING_PSYCHOLOGIST = 20;
    //письмо вручено
    const LETTER_WAS_GIVEN = 21;

    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * Платная
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $pay = null;

    /**
     * Документ
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $document = null;

    /**
     * Сумма
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $amount = null;

    /**
     * Комметарий
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $comment = null;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return ServiceType
     */
    public function setName(?string $name): ServiceType
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

    /**
     * Set pay
     *
     * @param boolean $pay
     *
     * @return ServiceType
     */
    public function setPay(?bool $pay): ServiceType
    {
        $this->pay = $pay;

        return $this;
    }

    /**
     * Get pay
     *
     * @return boolean
     */
    public function getPay(): ?bool
    {
        return $this->pay;
    }

    /**
     * Set document
     *
     * @param boolean $document
     *
     * @return ServiceType
     */
    public function setDocument(?bool $document): ServiceType
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return boolean
     */
    public function getDocument(): ?bool
    {
        return $this->document;
    }

    /**
     * Get amount
     *
     * @return boolean
     */
    public function getAmount(): ?bool
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param boolean $amount
     *
     * @return ServiceType
     */
    public function setAmount(?bool $amount): ServiceType
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get comment
     *
     * @return boolean
     */
    public function getComment(): ?bool
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param boolean $comment
     *
     * @return ServiceType
     */
    public function setComment(?bool $comment): ServiceType
    {
        $this->comment = $comment;

        return $this;
    }
}
