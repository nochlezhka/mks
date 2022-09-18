<?php

namespace AppBundle\Entity;

use AppBundle\Service\DownloadableInterface;
use Application\Sonata\UserBundle\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContext;
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
    private ?string $comment;

    /**
     * Номер
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $number;

    /**
     * Дата начала
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateFrom;

    /**
     * Дата завершения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $dateTo;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contracts")
     */
    private Client $client;

    /**
     * Статус
     * @ORM\ManyToOne(targetEntity="ContractStatus")
     */
    private ContractStatus $status;

    /**
     * Документ
     * @ORM\ManyToOne(targetEntity="Document")
     */
    private Document $document;

    /**
     * Пункты
     * @ORM\OneToMany(targetEntity="ContractItem", mappedBy="contract", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"date" = "DESC", "id" = "DESC"})
     */
    private Collection $items;

    /**
     * {@inheritdoc}
     */
    public function getNamePrefix(): string
    {
        return 'contract';
    }

    public function __toString()
    {
        return $this->getLabel();
    }

    public function getLabel(): string
    {
        $label = $this->getNumber();

        if ($this->getDateFrom() instanceof DateTime) {
            $label .= ' от ' . $this->getDateFrom()->format('d.m.Y');
        }

        if ($this->getDateTo() instanceof DateTime) {
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
    public function getDuration(): ?int
    {
        $dateTo = $this->dateTo;
        if($dateTo == null) return null;
        return $this->dateFrom->diff($dateTo)->m;
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return Contract
     */
    public function setComment(?string $comment): Contract
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
     * Set number
     *
     * @param string|null $number
     *
     * @return Contract
     */
    public function setNumber(?string $number): Contract
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Set dateFrom
     *
     * @param DateTime|null $dateFrom
     *
     * @return Contract
     */
    public function setDateFrom(?DateTime $dateFrom): Contract
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return DateTime
     */
    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param DateTime|null $dateTo
     *
     * @return Contract
     */
    public function setDateTo(?DateTime $dateTo): Contract
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return DateTime
     */
    public function getDateTo(): ?DateTime
    {
        return $this->dateTo;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Contract
     */
    public function setClient(Client $client = null): Contract
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
     * Set status
     *
     * @param ContractStatus|null $status
     *
     * @return Contract
     */
    public function setStatus(ContractStatus $status = null): Contract
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return ContractStatus
     */
    public function getStatus(): ContractStatus
    {
        return $this->status;
    }

    /**
     * Set document
     *
     * @param Document|null $document
     *
     * @return Contract
     */
    public function setDocument(Document $document = null): Contract
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * Add item
     *
     * @param ContractItem $item
     *
     * @return Contract
     */
    public function addItem(ContractItem $item): Contract
    {
        $item->setContract($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param ContractItem $item
     */
    public function removeItem(ContractItem $item)
    {
        $item->setContract();
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContext $context)
    {
        if (!$this->getItems()->count()) {
            $context->addViolation('Не указан пункт', ['items']);
        }
    }
}
