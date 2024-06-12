<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\BaseEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Sonata\UserBundle\Model\User as UserModel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Устанавливает для изменяемой сущности значения:
 * - когда изменено
 * - кем изменено
 */
#[AsDoctrineListener(event: Events::preUpdate)]
final readonly class PreUpdater
{
    use UserTrait;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->getUser();

        if (!($entity instanceof BaseEntityInterface)) {
            return;
        }

        $entity->setUpdatedAt($entity instanceof UserModel ? new \DateTime() : new \DateTimeImmutable());
        $entity->setUpdatedBy($user);
    }
}
