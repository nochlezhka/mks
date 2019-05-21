<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Notice;
use AppBundle\Entity\ResidentQuestionnaire;
use AppBundle\Entity\ShelterHistory;
use AppBundle\Entity\ShelterStatus;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


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
     * @param $filter
     * @param User $user
     * @return array
     */
    public function getMyClientsNotice($filter, User $user)
    {
        $result = [];

        $arContracts = $this->getAllActiveContracts($filter);

        foreach ($arContracts->getResult() as $itm) {
            $arAllUserClientsNotice = $this->getAllUserClientsNotice($itm->getClient(), $user);
            if (!empty($arAllUserClientsNotice['id'])) {
                $result[] = $arAllUserClientsNotice['id'];
            }
        }

        return $result;
    }

    /**
     * @param $filter
     * @param User $user
     * @return int
     */
    public function getMyClientsNoticeHeaderCount($filter, User $user)
    {
        return count($this->getMyClientsNotice($filter, $user));
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
        $dateTo3 = clone($shelterHistory->getDateTo());
        $dateTo3->modify('92 days');
        $dateTo6 = clone($shelterHistory->getDateTo());
        $dateTo6->modify('183 days');
        $dateTo12 = clone($shelterHistory->getDateTo());
        $dateTo12->modify('365 days');

        $type3 = $this
            ->getEntityManager()
            ->getRepository(Notice::class)
            ->findOneBy(['text' => 'Необходимо заполнить анкету проживающего (3 месяца)', 'client' => $client, 'createdBy' => $shelterHistory->getCreatedBy()]);
        if (!$type3) {
            $type3 = new Notice();
            $type3->setClient($client);
            $type3->setCreatedBy($shelterHistory->getCreatedBy());
            $type3->setDate($dateTo3);
            $type3->setText('Необходимо заполнить анкету проживающего (3 месяца)');
            $this->getEntityManager()->persist($type3);
            $this->getEntityManager()->flush();
        }
        $type6 = $this
            ->getEntityManager()
            ->getRepository(Notice::class)
            ->findOneBy(['text' => 'Необходимо заполнить анкету проживающего (6 месяцев)', 'client' => $client, 'createdBy' => $shelterHistory->getCreatedBy()]);
        if (!$type6) {
            $type6 = new Notice();
            $type6->setClient($client);
            $type6->setCreatedBy($shelterHistory->getCreatedBy());
            $type6->setDate($dateTo6);
            $type6->setText('Необходимо заполнить анкету проживающего (6 месяцев)');
            $this->getEntityManager()->persist($type6);
            $this->getEntityManager()->flush();
        }
        $type12 = $this
            ->getEntityManager()
            ->getRepository(Notice::class)
            ->findOneBy(['text' => 'Необходимо заполнить анкету проживающего (1 год)', 'client' => $client, 'createdBy' => $shelterHistory->getCreatedBy()]);
        if (!$type12) {
            $type12 = new Notice();
            $type12->setClient($client);
            $type12->setCreatedBy($shelterHistory->getCreatedBy());
            $type12->setDate($dateTo12);
            $type12->setText('Необходимо заполнить анкету проживающего (1 год)');
            $this->getEntityManager()->persist($type12);
            $this->getEntityManager()->flush();
        }

        return $notices;
    }
}
