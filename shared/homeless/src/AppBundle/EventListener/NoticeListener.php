<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Notice;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Устанавливает признак просмотренности напоминания текущим пользователем
 * при загрузке сущности
 */
class NoticeListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $user = $this->getUser();

        if ($entity instanceof Notice && $user instanceof User) {
            if ($entity->getViewedBy()->contains($user)) {
                $entity->setViewed(true);
            } else {
                $entity->setViewed(false);
            }
        }
    }

    public function getUser()
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
