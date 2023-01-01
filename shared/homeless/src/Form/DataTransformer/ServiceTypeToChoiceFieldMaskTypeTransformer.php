<?php

namespace App\Form\DataTransformer;

use App\Repository\ServiceTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ServiceTypeToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{
    private ServiceTypeRepository $serviceTypeRepository;

    public function __construct(ServiceTypeRepository $serviceTypeRepository)
    {
        $this->serviceTypeRepository = $serviceTypeRepository;
    }

    public function transform(mixed $value)
    {
        return $value?->getId();
    }

    public function reverseTransform(mixed $value)
    {
        if (null === $value) {
            return null;
        } else {
            return $this->serviceTypeRepository->find($value);
        }
    }
}
