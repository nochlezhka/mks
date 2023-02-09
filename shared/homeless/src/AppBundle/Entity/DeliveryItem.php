<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Вещь в пункте выдачи.
 * @ORM\Entity()
 */
class DeliveryItem extends BaseEntity
{

    // Категории (значения взяты из таблицы БД service_type)
    const CATEGORY_CLOTHES = 3; // одежда
    const CATEGORY_HYGIENE = 17; // гигиена
    const CATEGORY_CRUTCHES = 22; // другое
    const CATEGORY_REHABILITATION = 39; // Средства реабилитации
    const CATEGORY_DISHES = 45; //  Посуда
    const CATEGORY_GIFT = 46; // Подарок

    public static $CATEGORY_NAMES = [
        3 => "Одежда",
        17 => "Гигиена",
        22 => "Аксессуары",
        39 => "Костыли/трости",
        45 => "Посуда",
        46 => "Подарок"
    ];

    /**
     * Название
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * Категория
     * @ORM\Column(type="integer", nullable=false)
     */
    private $category;

    /**
     * Категория
     * @ORM\Column(type="integer", nullable=false)
     */
    private $limitDays;

    /**
     * Кем создано
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     */
    protected $createdBy;

    /**
     * Город
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Branch", mappedBy="deliveryItems")
     */
    protected $branches;


    public function __construct()
    {
        $this->branches = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * @return ArrayCollection|Branch[]
     */
    public function getBranches()
    {
        return $this->branches;
    }

    public function setBranches($branches)
    {
        if (count($branches) > 0) {
            foreach ($branches as $i) {
                $this->addBranch($i);
            }
        }
        return $this;
    }

    public function addBranch(Branch $branch)
    {
        if (!$this->branches->contains($branch)) {
            $this->branches[] = $branch;
            $branch->addDeliveryItem($this);
        }
        return $this;
    }

    public function removeBranch(Branch $branch)
    {
        $this->branches->removeElement($branch);
        $branch->removeDeliveryItem($this);
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DeliveryItem
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
     * Set category
     *
     * @param integer $category
     *
     * @return DeliveryItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategoryName()
    {
        return self::getCategoryNameById($this->category);
    }

    /**
     * Get category name by id
     *
     * @return string
     */
    public static function getCategoryNameById($categoryID)
    {
        return self::$CATEGORY_NAMES[$categoryID] ?: '';
    }

    /**
     * Set limitDays
     *
     * @param integer $limitDays
     *
     * @return DeliveryItem
     */
    public function setLimitDays($limitDays)
    {
        $this->limitDays = $limitDays;

        return $this;
    }

    /**
     * Get limitDays
     *
     * @return integer
     */
    public function getLimitDays()
    {
        return $this->limitDays;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getLimitDays() < 0) {
            $context->addViolationAt(
                'limitDays',
                'Не может быть отрицательным',
                [],
                null
            );
        }
    }

}
