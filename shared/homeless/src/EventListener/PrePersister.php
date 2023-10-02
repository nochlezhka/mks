<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\BaseEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Устанавливает для создаваемой сущности значения:
 * - когда создано
 * - кем создано
 */
#[AsDoctrineListener(event: Events::prePersist)]
readonly class PrePersister
{
    use UserTrait;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->getUser();

        if (!($entity instanceof BaseEntityInterface)) {
            return;
        }

        if (empty($entity->getCreatedAt())) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (empty($entity->getCreatedBy())) {
            $entity->setCreatedBy($user);
        }
    }
}
