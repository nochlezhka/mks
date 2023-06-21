<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponseValue;
use App\Entity\MenuItem;
use App\Entity\Notice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notice|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Notice|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<Notice> findAll()
 * @method array<Notice> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoticeRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly ContractRepository $contractRepository,
        private readonly MenuItemRepository $menuItemRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Notice::class);
    }

    /**
     * Количество непросмотренных пользователем напоминаний по данному клиенту
     *
     * @throws NonUniqueResultException
     */
    public function getUnviewedCount(Client $client, User $user): mixed
    {
        $result = $this
            ->createQueryBuilder('n')
            ->select('COUNT(n) as cnt')
            ->where('n.client = :client')
            ->andWhere(':user NOT MEMBER OF n.viewedBy')
            ->andWhere('n.date <= :now')
            ->setParameters([
                'client' => $client,
                'user' => $user,
                'now' => new \DateTimeImmutable(),
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['cnt'] ?? 0;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getAllUserClientsNotice(Client $client, User $user): ?array
    {
        return $this
            ->createQueryBuilder('n')
            ->select('n.id, n.text')
            ->where('n.client = :client')
            ->andWhere(':user NOT MEMBER OF n.viewedBy')
            ->andWhere('n.date <= :now')
            ->setParameters([
                'client' => $client,
                'user' => $user,
                'now' => new \DateTimeImmutable(),
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllActiveContracts(array $filter): Query
    {
        return $this->contractRepository
            ->createQueryBuilder('cont')
            ->where('cont.createdBy = :contractCreatedBy')
            ->andWhere('cont.status = :contractStatus')
            ->setParameters([
                'contractCreatedBy' => $filter['contractCreatedBy'],
                'contractStatus' => $filter['contractStatus'],
            ])
            ->getQuery()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMyClientsNoticeHeaderCount(mixed $filter, User $user): int
    {
        return \count($this->getMyClientsNoticeHeader($filter, $user));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMyClientsNoticeHeader(mixed $filter, User $user): array
    {
        $isNotification = $this->menuItemRepository->isEnableCode(MenuItem::CODE_NOTIFICATIONS);
        if (!$isNotification) {
            return [];
        }
        $result = [];

        $arContracts = $this->getAllActiveContracts($filter);

        foreach ($arContracts->getResult() as $itm) {
            $arAllUserClientsNotice = $this->getAllUserClientsNotice($itm->getClient(), $user);
            if ($arAllUserClientsNotice === null) {
                continue;
            }
            $result[$arAllUserClientsNotice['id']] = $arAllUserClientsNotice;
            $result[$arAllUserClientsNotice['id']]['client'] = $itm->getClient();
        }

        $isQuestionnaireLiving = $this->menuItemRepository->isEnableCode(MenuItem::CODE_QUESTIONNAIRE_LIVING);
        if ($isQuestionnaireLiving) {
            $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
            $qnrType3Mon = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS;
            $qnrType6Mon = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS;
            $qnrType1Year = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR;
            $qnrType2Years = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS;
            $maxResFormTTL = 9999;
            $residentFormsSubquery = "
                SELECT frv.client_id,
                    MAX(CASE frv.value
                        -- для каждого типа анкеты указано, когда нужно напомнить о заполнении следующей
                        WHEN '{$qnrType3Mon}' THEN 6
                        WHEN '{$qnrType6Mon}' THEN 12
                        WHEN '{$qnrType1Year}' THEN 24
                        -- если возвращается {$maxResFormTTL}, то больше не будет напоминаний
                        WHEN '{$qnrType2Years}' THEN {$maxResFormTTL}
                        ELSE 0
                        END
                    ) max_ttl_months
                FROM client_form_response_value frv
                WHERE frv.client_form_field_id = {$qnrTypeFieldId}
                GROUP BY frv.client_id
            ";
            $sql = "
                SELECT cl.*
                FROM client cl
                    JOIN (SELECT MAX(id) id, client_id FROM contract GROUP BY client_id) ct
                        ON ct.client_id= cl.id
                    JOIN contract c
                        ON c.id = ct.id
                    JOIN (SELECT MAX(id) id, client_id, MAX(date_to) date_to, MAX(date_from) date_from FROM shelter_history WHERE date_to IS NOT NULL GROUP BY client_id) sh
                        ON sh.client_id= c.client_id
                    LEFT JOIN ({$residentFormsSubquery}) res_forms
                        ON res_forms.client_id = c.client_id
                WHERE c.created_by_id = ?
                    -- у клиента нет ни одной заполненной анкеты, или есть, но не с максимально возможным типом
                    AND (res_forms.max_ttl_months IS NULL OR res_forms.max_ttl_months != {$maxResFormTTL})
                    -- по максимальному сроку анкеты понятно, что уже пора заполнять за следующий период
                    AND DATE_ADD(sh.date_to, INTERVAL IFNULL(res_forms.max_ttl_months, 3) MONTH) < NOW()
                    AND sh.date_to >= '2019-01-01';
            ";

            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
            $rsm->addRootEntityFromClassMetadata(Client::class, 'c');

            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $user->getId());

            /** @var array<Client> $clients */
            $clients = $query->getResult();
            foreach ($clients as $client) {
                $isSearch = false;
                foreach ($result as $item) {
                    if ($item['client']->getId() === $client->getId()) {
                        $isSearch = true;
                        break;
                    }
                }
                if (!$isSearch) {
                    $result[] = [
                        'text' => 'Необходимо заполнить анкету проживающего',
                        'client' => $client,
                    ];
                }
            }
        }

        return $result;
    }
}
