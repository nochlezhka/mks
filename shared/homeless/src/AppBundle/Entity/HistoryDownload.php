<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryDownload
 *
 * @ORM\Table(name="history_download")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryDownloadRepository")
 */
class HistoryDownload
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id = 0;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private Client $client;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private User $user;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private DateTime $date;

    /**
     * Тип сертификата
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CertificateType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="certificate_type_id", referencedColumnName="id")
     * })
     */
    private CertificateType $certificateType;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return HistoryDownload
     */
    public function setClient(Client $client = null): HistoryDownload
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Set user
     *
     * @param User|null $user
     *
     * @return HistoryDownload
     */
    public function setUser(User $user = null): HistoryDownload
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return HistoryDownload
     */
    public function setDate(DateTime $date): HistoryDownload
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Set certificate
     *
     * @param CertificateType $certificateType
     *
     * @return HistoryDownload
     */
    public function setCertificateType(CertificateType $certificateType): HistoryDownload
    {
        $this->certificateType = $certificateType;

        return $this;
    }

    /**
     * Get certificate
     *
     * @return CertificateType
     */
    public function getCertificateType(): CertificateType
    {
        return $this->certificateType;
    }
}
