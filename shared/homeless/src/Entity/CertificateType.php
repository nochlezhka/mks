<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип справки
 * @ORM\Entity(repositoryClass="App\Repository\CertificateTypeRepository")
 */
class CertificateType extends BaseEntity
{
    // SyncId различных типов
    // Стандартный документ
    const STANDARD = 1;
    // Справка о регистрации
    const REGISTRATION = 11;
    // Направление на санобработку
    const SANITATION = 12;
    // Справка для проезда
    const TRAVEL = 13;
    // Направление в диспансер
    const DISPENSARY = 14;
    // Справка о социальной помощи
    const HELP = 15;
    // Транзит
    const TRANSIT = 16;
    // Направление в центр занятности
    const EMPLOYMENT = 17;
    // Неизвестно
    const UNKNOWN = 20;

    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * Доступен для скачивания
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $downloadable = false;

    /**
     * Отображать фото клиента
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $showPhoto = false;

    /**
     * Отображать дату ниже ФИО сотрудника
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $showDate = false;

    /**
     * Содержимое верхнего левого блока
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $contentHeaderLeft = null;

    /**
     * Содержимое верхнего правого блока
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $contentHeaderRight = null;

    /**
     * Содержимое среднего блока
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $contentBodyRight = null;

    /**
     * Содержимое нижнего блока
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $contentFooter = null;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return CertificateType
     */
    public function setName(?string $name): CertificateType
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
     * Set downloadable
     *
     * @param boolean $downloadable
     *
     * @return CertificateType
     */
    public function setDownloadable(?bool $downloadable): CertificateType
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    /**
     * Get downloadable
     *
     * @return boolean
     */
    public function getDownloadable(): ?bool
    {
        return $this->downloadable;
    }

    /**
     * Set showPhoto
     *
     * @param boolean $showPhoto
     *
     * @return CertificateType
     */
    public function setShowPhoto(?bool $showPhoto): CertificateType
    {
        $this->showPhoto = $showPhoto;

        return $this;
    }

    /**
     * Get showPhoto
     *
     * @return boolean
     */
    public function getShowPhoto(): ?bool
    {
        return $this->showPhoto;
    }

    /**
     * Set showDate
     *
     * @param boolean $showDate
     *
     * @return CertificateType
     */
    public function setShowDate(?bool $showDate): CertificateType
    {
        $this->showDate = $showDate;

        return $this;
    }

    /**
     * Get showDate
     *
     * @return boolean
     */
    public function getShowDate(): ?bool
    {
        return $this->showDate;
    }

    /**
     * Set contentHeaderLeft
     *
     * @param string|null $contentHeaderLeft
     *
     * @return CertificateType
     */
    public function setContentHeaderLeft(?string $contentHeaderLeft): CertificateType
    {
        $this->contentHeaderLeft = $contentHeaderLeft;

        return $this;
    }

    /**
     * Get contentHeaderLeft
     *
     * @return string
     */
    public function getContentHeaderLeft(): ?string
    {
        return $this->contentHeaderLeft;
    }

    /**
     * Set contentHeaderRight
     *
     * @param string|null $contentHeaderRight
     *
     * @return CertificateType
     */
    public function setContentHeaderRight(?string $contentHeaderRight): CertificateType
    {
        $this->contentHeaderRight = $contentHeaderRight;

        return $this;
    }

    /**
     * Get contentHeaderRight
     *
     * @return string
     */
    public function getContentHeaderRight(): ?string
    {
        return $this->contentHeaderRight;
    }

    /**
     * Set contentBodyRight
     *
     * @param string|null $contentBodyRight
     *
     * @return CertificateType
     */
    public function setContentBodyRight(?string $contentBodyRight): CertificateType
    {
        $this->contentBodyRight = $contentBodyRight;

        return $this;
    }

    /**
     * Get contentBodyRight
     *
     * @return string
     */
    public function getContentBodyRight(): ?string
    {
        return $this->contentBodyRight;
    }

    /**
     * Set contentFooter
     *
     * @param string|null $contentFooter
     *
     * @return CertificateType
     */
    public function setContentFooter(?string $contentFooter): CertificateType
    {
        $this->contentFooter = $contentFooter;

        return $this;
    }

    /**
     * Get contentFooter
     *
     * @return string
     */
    public function getContentFooter(): ?string
    {
        return $this->contentFooter;
    }
}
