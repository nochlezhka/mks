<?php

namespace App\Admin;

use App\Entity\ContractStatus;
use App\Entity\MenuItem;
use App\Entity\User;
use App\Repository\MenuItemRepository;
use App\Repository\NoticeRepository;
use App\Service\MetaService;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait BaseAdminTrait
{
    protected MenuItemRepository $menuItemRepository;
    protected NoticeRepository $noticeRepository;

    protected ManagerRegistry $manager;
    protected MetaService $metaService;
    protected TokenStorageInterface $tokenStorage;

    public function getMyClientsNoticeCount(): int
    {
        if (!$this->menuItemRepository
            ->isEnableCode(MenuItem::CODE_NOTIFICATIONS)) {
            return 0;
        }
        $user = $this->tokenStorage->getToken()->getUser();

        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this
            ->manager
            ->getRepository(ContractStatus::class)
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        if(!($user instanceof User)) {
            throw new InvalidArgumentException("Unexpected User type");
        }

        return $this->noticeRepository
            ->getMyClientsNoticeHeaderCount($filter, $user, $this->metaService->isClientFormsEnabled());
    }

    /**
     * Получение заголовков оповещений клиентов для текущего пользователя
     *
     */
    public function getMyClientsNoticeHeader(): array
    {
        if (!$this->menuItemRepository
            ->isEnableCode(MenuItem::CODE_NOTIFICATIONS)) {
            return [];
        }
        $user = $this
            ->tokenStorage
            ->getToken()
            ->getUser();

        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this
            ->manager
            ->getRepository(ContractStatus::class)
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        if(!($user instanceof User)) {
            throw new InvalidArgumentException("Unexpected User type");
        }

        return $this->noticeRepository
            ->getMyClientsNoticeHeader($filter, $user, $this->metaService->isClientFormsEnabled());
    }

    #[Required]
    public function setManagerRegistry(ManagerRegistry $manager): void
    {
        $this->manager = $manager;
    }

    #[Required]
    public function setMetaService(MetaService $metaService): void
    {
        $this->metaService = $metaService;
    }

    #[Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Required]
    public function setNoticeRepository(NoticeRepository $noticeRepository): void
    {
        $this->noticeRepository = $noticeRepository;
    }

    #[Required]
    public function setMenuItemRepository(MenuItemRepository $menuItemRepository): void
    {
        $this->menuItemRepository = $menuItemRepository;
    }
}
