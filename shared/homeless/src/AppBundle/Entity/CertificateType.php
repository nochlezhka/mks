<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Тип справки
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CertificateTypeRepository")
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
    private $name;

    /**
     * Доступен для скачивания
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $downloadable = false;

    /**
     * Отображать фото клиента
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showPhoto = false;

    /**
     * Отображать дату ниже ФИО сотрудника
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showDate = false;

    /**
     * Содержимое верхнего левого блока
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentHeaderLeft;

    /**
     * Содержимое верхнего правого блока
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentHeaderRight;

    /**
     * Содержимое среднего блока
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentBodyRight;

    /**
     * Содержимое нижнего блока
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentFooter;

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CertificateType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
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
    public function setDownloadable($downloadable)
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    /**
     * Get downloadable
     *
     * @return boolean
     */
    public function getDownloadable()
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
    public function setShowPhoto($showPhoto)
    {
        $this->showPhoto = $showPhoto;

        return $this;
    }

    /**
     * Get showPhoto
     *
     * @return boolean
     */
    public function getShowPhoto()
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
    public function setShowDate($showDate)
    {
        $this->showDate = $showDate;

        return $this;
    }

    /**
     * Get showDate
     *
     * @return boolean
     */
    public function getShowDate()
    {
        return $this->showDate;
    }

    /**
     * Set contentHeaderLeft
     *
     * @param string $contentHeaderLeft
     *
     * @return CertificateType
     */
    public function setContentHeaderLeft($contentHeaderLeft)
    {
        $this->contentHeaderLeft = $contentHeaderLeft;

        return $this;
    }

    /**
     * Get contentHeaderLeft
     *
     * @return string
     */
    public function getContentHeaderLeft()
    {
        return $this->contentHeaderLeft;
    }

    /**
     * Set contentHeaderRight
     *
     * @param string $contentHeaderRight
     *
     * @return CertificateType
     */
    public function setContentHeaderRight($contentHeaderRight)
    {
        $this->contentHeaderRight = $contentHeaderRight;

        return $this;
    }

    /**
     * Get contentHeaderRight
     *
     * @return string
     */
    public function getContentHeaderRight()
    {
        return $this->contentHeaderRight;
    }

    /**
     * Set contentBodyRight
     *
     * @param string $contentBodyRight
     *
     * @return CertificateType
     */
    public function setContentBodyRight($contentBodyRight)
    {
        $this->contentBodyRight = $contentBodyRight;

        return $this;
    }

    /**
     * Get contentBodyRight
     *
     * @return string
     */
    public function getContentBodyRight()
    {
        return $this->contentBodyRight;
    }

    /**
     * Set contentFooter
     *
     * @param string $contentFooter
     *
     * @return CertificateType
     */
    public function setContentFooter($contentFooter)
    {
        $this->contentFooter = $contentFooter;

        return $this;
    }

    /**
     * Get contentFooter
     *
     * @return string
     */
    public function getContentFooter()
    {
        return $this->contentFooter;
    }
}
