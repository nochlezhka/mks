<?php

namespace App\Form\DataTransformer;

use App\Repository\CertificateTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;

class CertificateTypeToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{

    /** @var  CertificateTypeRepository */
    private $certificateTypeRepository;

    public function __construct(CertificateTypeRepository $certificateTypeRepository)
    {
        $this->certificateTypeRepository = $certificateTypeRepository;
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
            $result = $this->certificateTypeRepository->find($value);
            return $result;
        }
    }
}
