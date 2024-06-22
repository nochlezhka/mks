<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\PositionRepository;
use Symfony\Component\Form\DataTransformerInterface;

final readonly class PositionToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{
    public function __construct(
        private PositionRepository $positionRepository,
    ) {}

    public function transform(mixed $value): mixed
    {
        return $value?->getId();
    }

    public function reverseTransform(mixed $value): ?object
    {
        if ($value === null) {
            return null;
        }

        return $this->positionRepository->find($value);
    }
}
