<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\ServiceTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;

final readonly class ServiceTypeToChoiceFieldMaskTypeTransformer implements DataTransformerInterface
{
    public function __construct(
        private ServiceTypeRepository $serviceTypeRepository,
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

        return $this->serviceTypeRepository->find($value);
    }
}
