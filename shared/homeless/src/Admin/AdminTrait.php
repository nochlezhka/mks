<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ContractStatus;
use App\Entity\MenuItem;
use App\Entity\User;
use App\Repository\ContractStatusRepository;
use App\Repository\MenuItemRepository;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait AdminTrait
{
    protected TokenStorageInterface $tokenStorage;
    protected EntityManagerInterface $entityManager;
    protected MenuItemRepository $menuItemRepository;
    protected NoticeRepository $noticeRepository;
    protected ContractStatusRepository $contractStatusRepository;

    #[Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    #[Required]
    public function setMenuItemRepository(MenuItemRepository $menuItemRepository): void
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    #[Required]
    public function setNoticeRepository(NoticeRepository $noticeRepository): void
    {
        $this->noticeRepository = $noticeRepository;
    }

    #[Required]
    public function setContractStatusRepository(ContractStatusRepository $contractStatusRepository): void
    {
        $this->contractStatusRepository = $contractStatusRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMyClientsNoticeCount(): int
    {
        if (!$this->menuItemRepository->isEnableCode(MenuItem::CODE_NOTIFICATIONS)) {
            return 0;
        }

        $user = $this->getUser();
        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this->contractStatusRepository->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);
        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string) $inProcessStatus->getId()]];
        }

        if (!($user instanceof User)) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        return \count($this->noticeRepository->getMyClientsNoticeHeader($filter, $user));
    }

    /**
     * Получение заголовков оповещений клиентов для текущего пользователя
     *
     * @throws NonUniqueResultException
     */
    public function getMyClientsNoticeHeader(): array
    {
        if (!$this->menuItemRepository->isEnableCode(MenuItem::CODE_NOTIFICATIONS)) {
            return [];
        }

        $user = $this->getUser();
        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this->contractStatusRepository->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);
        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string) $inProcessStatus->getId()]];
        }

        if (!($user instanceof User)) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        return $this->noticeRepository->getMyClientsNoticeHeader($filter, $user);
    }

    protected function getUser(): ?UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
