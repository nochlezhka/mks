<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

trait UserTrait
{
    private readonly TokenStorageInterface $tokenStorage;

    protected function getUser(): ?User
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
