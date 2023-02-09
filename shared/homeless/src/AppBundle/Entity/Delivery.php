<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Выданная клиенту вещь в пункте выдачи.
 * @package AppBundle\Entity
 * @ORM\Entity()
 */
class Delivery extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="deliveries")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryItem")
     */
    private $deliveryItem;

    /**
     * @var \Date
     *
     * @ORM\Column(name="delivered_at", type="date")
     */
    private $deliveredAt;

    /**
     * Кем создано
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", fetch="EXTRA_LAZY")
     */
    protected $createdBy;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Delivery
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @return Delivery
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeliveryItem()
    {
        return $this->deliveryItem;
    }

    /**
     * @param mixed $item
     * @return Delivery
     */
    public function setDeliveryItem($item)
    {
        $this->deliveryItem = $item;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * @param \DateTime $deliveredAt
     * @return Delivery
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }

}
