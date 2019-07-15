<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\MenuItem;
use AppBundle\Entity\Notice;
use AppBundle\Entity\ResidentQuestionnaire;
use AppBundle\Entity\ShelterHistory;
use AppBundle\Entity\ShelterStatus;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class NoticeRepository extends EntityRepository
{
    /**
     * Количество непросмотренных пользователем напоминаний по данному клиенту
     * @param Client $client
     * @param User $user
     * @return mixed
     */
    public function getUnviewedCount(Client $client, User $user)
    {
        $result = $this
            ->createQueryBuilder('n')
            ->select('COUNT(n) as cnt')
            ->where('n.client = :client')
            ->andWhere(':user NOT MEMBER OF n.viewedBy')
            ->andWhere('n.date <= :now')
            ->setParameters(['client' => $client, 'user' => $user, 'now' => new \DateTime()])
            ->getQuery()
            ->getOneOrNullResult();

        return $result['cnt'];
    }

    /**
     * @param Client $client
     * @param User $user
     * @return mixed
     */
    public function getAllUserClientsNotice(Client $client, User $user)
    {
        $result = $this
            ->createQueryBuilder('n')
            ->select('n.id, n.text')
            ->where('n.client = :client')
            ->andWhere(':user NOT MEMBER OF n.viewedBy')
            ->andWhere('n.date <= :now')
            ->setParameters(['client' => $client, 'user' => $user, 'now' => new \DateTime()])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @param array $filter
     * @return \Doctrine\ORM\Query
     */
    public function getAllActiveContracts(array $filter)
    {
        $result = $this
            ->getEntityManager()
            ->getRepository('AppBundle:Contract')
            ->createQueryBuilder('cont')
            ->where('cont.createdBy=:contractCreatedBy')
            ->andWhere('cont.status=:contractStatus')
            ->setParameters([
                'contractCreatedBy' => $filter['contractCreatedBy'],
                'contractStatus' => $filter['contractStatus'],
            ])
            ->getQuery();

        return $result;
    }

    /**
     * @param array $filter
     * @return \Doctrine\ORM\Query
     */
    public function getAllContractsResidentQuestionnaire(array $filter)
    {
        $result = $this
            ->getEntityManager()
            ->getRepository('AppBundle:Contract')
            ->createQueryBuilder('cont')
            ->where('cont.createdBy=:contractCreatedBy')
            ->setParameters([
                'contractCreatedBy' => $filter['contractCreatedBy'],
            ])
            ->getQuery();

        return $result;
    }

    /**
     * @param $filter
     * @param User $user
     * @return int
     */
    public function getMyClientsNoticeHeaderCount($filter, User $user)
    {
        return count($this->getMyClientsNoticeHeader($filter, $user));
    }

    /**
     * @param $filter
     * @param User $user
     * @return array
     */
    public function getMyClientsNoticeHeader($filter, User $user)
    {
        $result = [];

        $arContracts = $this->getAllActiveContracts($filter);

        foreach ($arContracts->getResult() as $itm) {
            $arAllUserClientsNotice = $this->getAllUserClientsNotice($itm->getClient(), $user);
            if (null === $arAllUserClientsNotice) {
                continue;
            }
            $result[$arAllUserClientsNotice['id']] = $arAllUserClientsNotice;
            $result[$arAllUserClientsNotice['id']]['client'] = $itm->getClient();
        }

        /** @var MenuItem $menuItemShelterHistory */
        $menuItemShelterHistory = $this
            ->getEntityManager()
            ->getRepository(MenuItem::class)
            ->findByCode(MenuItem::CODE_SHELTER_HISTORY);
        if ($menuItemShelterHistory && $menuItemShelterHistory->getEnabled()) {
            $sql = "SELECT cl.*
                FROM client cl
                JOIN (SELECT MAX(id) id, client_id FROM contract GROUP BY client_id) ct ON ct.client_id= cl.id
                JOIN contract c on c.id = ct.id
                JOIN (SELECT MAX(id) id, client_id, MAX(date_to) date_to, MAX(date_from) date_from FROM shelter_history WHERE date_to IS NOT NULL GROUP BY client_id) sh ON sh.client_id= c.client_id
                LEFT JOIN resident_questionnaire rq3 ON rq3.client_id = c.client_id AND rq3.type_id = 1
                LEFT JOIN resident_questionnaire rq6 ON rq6.client_id = c.client_id AND rq6.type_id = 2
                LEFT JOIN resident_questionnaire rq12 ON rq12.client_id = c.client_id AND rq12.type_id = 3
                WHERE c.created_by_id = ? AND (
                        (rq3.id IS NULL AND rq6.id IS NULL AND rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 3 MONTH) < NOW()) OR
                        (rq6.id IS NULL AND rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 6 MONTH) < NOW()) OR
                        (rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 12 MONTH) < NOW())
                    ) AND sh.date_to >= '2019-01-01';";

            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
            $rsm->addRootEntityFromClassMetadata(Client::class, 'c');

            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $user->getId());

            /** @var Client[] $clients */
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

    /**
     * Автоматические напоминания
     * @param Client $client
     * @return mixed
     */
    public function getAutoNotices(Client $client)
    {
        $shelterHistory = $this
            ->getEntityManager()
            ->getRepository(ShelterHistory::class)
            ->createQueryBuilder('sh')
            ->where('sh.client = :client')
            ->orderBy('sh.dateTo', 'ASC')
            ->addOrderBy('sh.id', 'ASC')
            ->setParameters(['client' => $client])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $notices = [];

        if (!$shelterHistory instanceof ShelterHistory || !$shelterHistory->getDateTo()) {
            return $notices;
        }

        return $notices;
    }
}
