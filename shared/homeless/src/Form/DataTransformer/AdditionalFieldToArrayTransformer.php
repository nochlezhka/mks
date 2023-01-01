<?php

namespace App\Form\DataTransformer;

use App\Entity\ClientFieldOption;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class AdditionalFieldToArrayTransformer
 */
class AdditionalFieldToArrayTransformer implements DataTransformerInterface
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function transform(mixed $value)
    {
        if (null === $value) {
            return null;
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

    public function reverseTransform(mixed $value)
    {
        if (null === $value) {
            return null;
        }

        if (is_array($value)) {
            $newResult = new ArrayCollection();
            $result = $this->doctrine->getRepository(ClientFieldOption::class)->findBy(['id' => $value]);
            foreach ($result as $item) {
                $newResult->add($item);
            }
            $result = $newResult;

        } else {
            $result = $this->doctrine->getRepository(ClientFieldOption::class)->find($value);
        }

        return $result;
    }
}
