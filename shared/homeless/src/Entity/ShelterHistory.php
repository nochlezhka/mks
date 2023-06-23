<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Данные о проживании в приюте (договор о заселении)
 */
#[ORM\Entity]
class ShelterHistory extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    /**
     * Дата прививки от дифтерии
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $diphtheriaVaccinationDate = null;

    /**
     * Дата флюорографии
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $fluorographyDate = null;

    /**
     * Дата прививки от гепатита
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $hepatitisVaccinationDate = null;

    /**
     * Дата прививки от тифа
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $typhusVaccinationDate = null;

    /**
     * Дата заселения
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateFrom = null;

    /**
     * Дата выселения
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateTo = null;

    #[ORM\ManyToOne(targetEntity: ShelterRoom::class)]
    private ?ShelterRoom $room = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'shelterHistories')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: ShelterStatus::class)]
    private ?ShelterStatus $status = null;

    #[ORM\ManyToOne(targetEntity: Contract::class)]
    private ?Contract $contract = null;

    public function __toString(): string
    {
        return $this->status?->getName() ?? '';
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDiphtheriaVaccinationDate(): ?\DateTimeImmutable
    {
        return $this->diphtheriaVaccinationDate;
    }

    public function setDiphtheriaVaccinationDate(?\DateTimeImmutable $diphtheriaVaccinationDate): self
    {
        $this->diphtheriaVaccinationDate = $diphtheriaVaccinationDate;

        return $this;
    }

    public function getFluorographyDate(): ?\DateTimeImmutable
    {
        return $this->fluorographyDate;
    }

    public function setFluorographyDate(?\DateTimeImmutable $fluorographyDate): self
    {
        $this->fluorographyDate = $fluorographyDate;

        return $this;
    }

    public function getHepatitisVaccinationDate(): ?\DateTimeImmutable
    {
        return $this->hepatitisVaccinationDate;
    }

    public function setHepatitisVaccinationDate(?\DateTimeImmutable $hepatitisVaccinationDate): self
    {
        $this->hepatitisVaccinationDate = $hepatitisVaccinationDate;

        return $this;
    }

    public function getTyphusVaccinationDate(): ?\DateTimeImmutable
    {
        return $this->typhusVaccinationDate;
    }

    public function setTyphusVaccinationDate(?\DateTimeImmutable $typhusVaccinationDate): self
    {
        $this->typhusVaccinationDate = $typhusVaccinationDate;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeImmutable $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeImmutable $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getRoom(): ?ShelterRoom
    {
        return $this->room;
    }

    public function setRoom(ShelterRoom $room): self
    {
        $this->room = $room;

        return $this;
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

    public function getStatus(): ?ShelterStatus
    {
        return $this->status;
    }

    public function setStatus(ShelterStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }
}
