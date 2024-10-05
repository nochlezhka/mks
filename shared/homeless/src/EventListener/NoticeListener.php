<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

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
final readonly class NoticeListener
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function postLoad(Notice $notice, LifecycleEventArgs $args): void
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
