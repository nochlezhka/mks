<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Repository\PositionRepository;
use Symfony\Component\Form\DataTransformerInterface;

class PositionToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{

    /** @var  PositionRepository */
    private $positionRepository;

    public function __construct(PositionRepository $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    /**
     * @param mixed $value
     * @return null
     */
    public function transform($value)
    {
        if (null === $value) {
            return $value;
        }

        return $value->getId();
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
            $result = $this->positionRepository->find($value);
            return $result;
        }
    }
}
