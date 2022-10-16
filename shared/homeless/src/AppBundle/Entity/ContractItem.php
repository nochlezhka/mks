<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Пункт договора (сервисного плана)
 * @ORM\Entity()
 */
class ContractItem extends BaseEntity
{
    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * Дата начала выполнения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateStart = null;

    /**
     * Дата выполнения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $date = null;

    /**
     * Договор
     * @ORM\ManyToOne(targetEntity="Contract", inversedBy="items")
     */
    private Contract $contract;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="ContractItemType")
     */
    private ContractItemType $type;

    public function __toString()
    {
        $type = $this->getType();

        if ($type instanceof ContractItemType) {
            return $type->getName();
        }

        return '';
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return ContractItem
     */
    public function setComment(?string $comment): ContractItem
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param DateTime|null $date
     *
     * @return ContractItem
     */
    public function setDate(?DateTime $date): ContractItem
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * Set contract
     *
     * @param Contract|null $contract
     *
     * @return ContractItem
     */
    public function setContract(Contract $contract = null): ContractItem
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Get contract
     *
     * @return Contract
     */
    public function getContract(): Contract
    {
        return $this->contract;
    }

    /**
     * Set type
     *
     * @param ContractItemType|null $type
     *
     * @return ContractItem
     */
    public function setType(ContractItemType $type = null): ContractItem
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return ContractItemType
     */
    public function getType(): ContractItemType
    {
        return $this->type;
    }

    /**
     * @return DateTime|null
     */
    public function getDateStart(): ?DateTime
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime|null $dateStart
     */
    public function setDateStart(?DateTime $dateStart)
    {
        $this->dateStart = $dateStart;
    }
}
