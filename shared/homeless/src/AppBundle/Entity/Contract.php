<?php

namespace AppBundle\Entity;

use AppBundle\Service\DownloadableInterface;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Договор (сервисный план)
 * @ORM\Entity()
 */
class Contract extends BaseEntity implements DownloadableInterface
{
    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * Дата начала
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFrom;

    /**
     * Дата завершения
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateTo;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contracts")
     */
    private $client;

    /**
     * Статус
     * @ORM\ManyToOne(targetEntity="ContractStatus")
     */
    private $status;

    /**
     * Документ
     * @ORM\ManyToOne(targetEntity="Document")
     */
    private $document;

    /**
     * Пункты
     * @ORM\OneToMany(targetEntity="ContractItem", mappedBy="contract", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"date" = "DESC", "id" = "DESC"})
     */
    private $items;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix()
    {
        return 'contract';
    }

    public function __toString()
    {
        return (string)$this->getLabel();
    }

    public function getLabel()
    {
        $label = $this->getNumber();

        if ($this->getDateFrom() instanceof \DateTime) {
            $label .= ' от ' . $this->getDateFrom()->format('d.m.Y');
        }

        if ($this->getDateTo() instanceof \DateTime) {
            $label .= ' до ' . $this->getDateTo()->format('d.m.Y');
        }

        if ($this->getStatus() instanceof ContractStatus) {
            $label .= ' (' . $this->getStatus() . ')';
        }

        if ($this->getCreatedBy() instanceof User) {
            $label .= ', ' . $this->getCreatedBy();
        }

        return $label;
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __set($name, $value)
    {

    }

    /**
     * Срок действия договора в месяцах
     * @return int
     */
    public function getDuration()
    {
        if ($this->dateFrom instanceof \DateTime && $this->dateTo instanceof \DateTime) {
            return $this->dateFrom->diff($this->dateTo)->m;
        }

        return null;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Contract
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
     * Set number
     *
     * @param string $number
     *
     * @return Contract
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return Contract
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return Contract
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Contract
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
     * Set status
     *
     * @param \AppBundle\Entity\ContractStatus $status
     *
     * @return Contract
     */
    public function setStatus(ContractStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\ContractStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set document
     *
     * @param \AppBundle\Entity\Document $document
     *
     * @return Contract
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \AppBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Add item
     *
     * @param \AppBundle\Entity\ContractItem $item
     *
     * @return Contract
     */
    public function addItem(ContractItem $item)
    {
        $item->setContract($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\ContractItem $item
     */
    public function removeItem(ContractItem $item)
    {
        $item->setContract(null);
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->getItems()->count()) {
            $context->addViolationAt(
                'items',
                'Не указан пункт',
                [],
                null
            );
        }
    }
}
