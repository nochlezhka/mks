<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Repository\BranchRepository;
use Symfony\Component\Form\DataTransformerInterface;

class BranchToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{

    /** @var  BranchRepository */
    private $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
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
            $result = $this->branchRepository->find($value);
            return $result;
        }
    }
}
