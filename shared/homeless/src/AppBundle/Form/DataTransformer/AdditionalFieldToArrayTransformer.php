<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Repository\ClientFieldOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class AdditionalFieldToArrayTransformer
 * @package AppBundle\Form\DataTransformer
 */
class AdditionalFieldToArrayTransformer implements DataTransformerInterface
{

    /** @var  ClientFieldOptionRepository */
    private $clientFieldOptionRepository;

    /**
     * AdditionalFieldToArrayTransformer constructor.
     * @param ClientFieldOptionRepository $clientFieldEntityRepository
     */
    public function __construct(ClientFieldOptionRepository $clientFieldEntityRepository)
    {
        $this->clientFieldOptionRepository = $clientFieldEntityRepository;
    }

    /**
     * @param mixed $value
     * @return null
     */
    public function transform($value)
    {
        if(null === $value){
            return $value;
        }

        $result = null;

        if ($value instanceof PersistentCollection) {
            foreach ($value as $item) {
                $result[$item->getName()] = $item->getId();
            }
        } else {
            $result = $value->getId();
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @return null|object
     */
    public function reverseTransform($value)
    {
        $result = null;

        if (null === $value) {
            return $result;
        } else {
            if (is_array($value)) {
                $newResult = new ArrayCollection();
                $result = $this->clientFieldOptionRepository->findBy(['id' => $value]);
                foreach ($result as $item){
                    $newResult->add($item);
                }
                $result = $newResult;

            } else {
                $result = $this->clientFieldOptionRepository->find($value);
            }

            return $result;
        }
    }
}
