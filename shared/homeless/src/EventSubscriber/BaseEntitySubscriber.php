<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\EventSubscriber;

use App\Entity\BaseEntityInterface;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Подписчик на события базовой сущности
 * Устанавливает для создаваемой/изменяемой сущности значения:
 * когда создано, кем создано, когда изменено, кем изменено
 */
#[AutoconfigureTag(name: 'doctrine.event_subscriber', attributes: ['connection' => 'default'])]
readonly class BaseEntitySubscriber implements EventSubscriber
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

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

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->getUser();

        if (!($entity instanceof BaseEntityInterface)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());
        $entity->setUpdatedBy($user);
    }

    public function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (!$token instanceof TokenInterface) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
