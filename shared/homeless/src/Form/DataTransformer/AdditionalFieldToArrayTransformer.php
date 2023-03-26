<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Form\DataTransformer;

use App\Repository\ClientFieldOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\DataTransformerInterface;

readonly class AdditionalFieldToArrayTransformer implements DataTransformerInterface
{
    public function __construct(
        private ClientFieldOptionRepository $clientFieldOptionRepository,
    ) {}

    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof PersistentCollection) {
            return $value->getId();
        }

        $result = [];
        foreach ($value as $item) {
            $result[$item->getName()] = $item->getId();
        }

        return $result;
    }

    public function reverseTransform(mixed $value): ?object
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            return $this->clientFieldOptionRepository->find($value);
        }

        return new ArrayCollection($this->clientFieldOptionRepository->findBy(['id' => $value]));
    }
}
