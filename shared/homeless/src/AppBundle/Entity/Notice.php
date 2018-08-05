<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Напоминание
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoticeRepository")
 */
class Notice extends BaseEntity
{
    /**
     * Текст
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * Дата
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client")
     */
    private $client;

    /**
     * Кем просмотрено
     * @ORM\ManyToMany(targetEntity="Application\Sonata\UserBundle\Entity\User", mappedBy="viewedNotices")
     */
    private $viewedBy;

    public function __toString()
    {
        return mb_substr($this->text, 0, 20) . '...';
    }

    public function __construct()
    {
        $this->viewedBy = new ArrayCollection();
    }

    /**
     * Просмотрено текущим пользователем
     */
    private $viewed = false;

    /**
     * @return mixed
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * @param mixed $viewed
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Notice
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Notice
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Notice
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
     * Add viewedBy
     *
     * @param \Application\Sonata\UserBundle\Entity\User $viewedBy
     *
     * @return Notice
     */
    public function addViewedBy(User $viewedBy)
    {
        $this->viewedBy[] = $viewedBy;
        $viewedBy->addViewedNotice($this);

        return $this;
    }

    /**
     * Remove viewedBy
     *
     * @param \Application\Sonata\UserBundle\Entity\User $viewedBy
     */
    public function removeViewedBy(User $viewedBy)
    {
        $this->viewedBy->removeElement($viewedBy);
        $viewedBy->removeViewedNotice($this);
    }

    /**
     * Get viewedBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViewedBy()
    {
        return $this->viewedBy;
    }
}
