<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Repository\ServiceTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ServiceTypeToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{

    /** @var  ServiceTypeRepository */
    private $serviceTypeRepository;

    public function __construct(ServiceTypeRepository $serviceTypeRepository)
    {
        $this->serviceTypeRepository = $serviceTypeRepository;
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
            $result = $this->serviceTypeRepository->find($value);
            return $result;
        }
    }
}
