<?php

namespace App\Form\DataTransformer;

use App\Entity\Position;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;

class PositionToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function transform(mixed $value)
    {
        return $value?->getId();

    }

    public function reverseTransform(mixed $value)
    {
        if (null === $value) {
            return null;
        }
        return $this->managerRegistry->getRepository(Position::class)->find($value);
    }
}
