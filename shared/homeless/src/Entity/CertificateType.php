<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CertificateTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Тип справки
 */
#[ORM\Entity(repositoryClass: CertificateTypeRepository::class)]
class CertificateType extends BaseEntity
{
    // SyncId различных типов
    // Стандартный документ
    public const int STANDARD = 1;
    // Справка о регистрации
    public const int REGISTRATION = 11;
    // Направление на санобработку
    public const int SANITATION = 12;
    // Справка для проезда
    public const int TRAVEL = 13;
    // Направление в диспансер
    public const int DISPENSARY = 14;
    // Справка о социальной помощи
    public const int HELP = 15;
    // Транзит
    public const int TRANSIT = 16;
    // Направление в центр занятности
    public const int EMPLOYMENT = 17;
    // Неизвестно
    public const int UNKNOWN = 20;

    /**
     * Название
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    /**
     * Доступен для скачивания
     */
    #[ORM\Column(type: 'boolean')]
    private bool $downloadable = false;

    /**
     * Отображать фото клиента
     */
    #[ORM\Column(type: 'boolean')]
    private bool $showPhoto = false;

    /**
     * Отображать дату ниже ФИО сотрудника
     */
    #[ORM\Column(type: 'boolean')]
    private bool $showDate = false;

    /**
     * Содержимое верхнего левого блока
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contentHeaderLeft = null;

    /**
     * Содержимое верхнего правого блока
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contentHeaderRight = null;

    /**
     * Содержимое среднего блока
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contentBodyRight = null;

    /**
     * Содержимое нижнего блока
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contentFooter = null;

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

    public function isDownloadable(): bool
    {
        return $this->downloadable;
    }

    public function setDownloadable(bool $downloadable): self
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    public function getShowPhoto(): bool
    {
        return $this->showPhoto;
    }

    public function setShowPhoto(bool $showPhoto): self
    {
        $this->showPhoto = $showPhoto;

        return $this;
    }

    public function getShowDate(): bool
    {
        return $this->showDate;
    }

    public function setShowDate(bool $showDate): self
    {
        $this->showDate = $showDate;

        return $this;
    }

    public function getContentHeaderLeft(): ?string
    {
        return $this->contentHeaderLeft;
    }

    public function setContentHeaderLeft(?string $contentHeaderLeft): self
    {
        $this->contentHeaderLeft = $contentHeaderLeft;

        return $this;
    }

    public function getContentHeaderRight(): ?string
    {
        return $this->contentHeaderRight;
    }

    public function setContentHeaderRight(?string $contentHeaderRight): self
    {
        $this->contentHeaderRight = $contentHeaderRight;

        return $this;
    }

    public function getContentBodyRight(): ?string
    {
        return $this->contentBodyRight;
    }

    public function setContentBodyRight(?string $contentBodyRight): self
    {
        $this->contentBodyRight = $contentBodyRight;

        return $this;
    }

    public function getContentFooter(): ?string
    {
        return $this->contentFooter;
    }

    public function setContentFooter(?string $contentFooter): self
    {
        $this->contentFooter = $contentFooter;

        return $this;
    }
}
