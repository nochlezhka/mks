<?php

namespace AppBundle\Entity;

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
    private $comment;

    /**
     * Дата начала выполнения
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateStart;

    /**
     * Дата выполнения
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * Договор
     * @ORM\ManyToOne(targetEntity="Contract", inversedBy="items")
     */
    private $contract;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="ContractItemType")
     */
    private $type;

    public function __toString()
    {
        $type = $this->getType();

        if ($type instanceof ContractItemType) {
            return (string)$type->getName();
        }

        return '';
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return ContractItem
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ContractItem
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
     * Set contract
     *
     * @param \AppBundle\Entity\Contract $contract
     *
     * @return ContractItem
     */
    public function setContract(Contract $contract = null)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Get contract
     *
     * @return \AppBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\ContractItemType $type
     *
     * @return ContractItem
     */
    public function setType(ContractItemType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\ContractItemType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param mixed $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }
}
