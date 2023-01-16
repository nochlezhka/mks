<?php

namespace App\EventSubscriber;

use App\Entity\BaseEntityInterface;
use App\Entity\User;
use DateTime;
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
class BaseEntitySubscriber implements EventSubscriber
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents(): array
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $user = $this->getUser();

        if ($entity instanceof BaseEntityInterface) {
            if (empty($entity->getCreatedAt())) {
                $entity->setCreatedAt(new DateTime());
            }

            if (empty($entity->getCreatedBy())) {
                $entity->setCreatedBy($user);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        $user = $this->getUser();

        if ($entity instanceof BaseEntityInterface) {
            $entity->setUpdatedAt(new DateTime());
            $entity->setUpdatedBy($user);
        }
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