<?php

namespace App\EventListener;

use App\Entity\Notice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Устанавливает признак просмотренности напоминания текущим пользователем
 * при загрузке сущности
 */
#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: Notice::class)]
class NoticeListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(Notice $notice, LifecycleEventArgs $args)
    {
        $user = $this->getUser();
        if ($notice->getViewedBy()->contains($user)) {
            $notice->setViewed(true);
        } else {
            $notice->setViewed(false);
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