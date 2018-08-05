<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
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
     * Автоматические напоминания
     * @param Client $client
     * @return mixed
     */
    public function getAutoNotices(Client $client)
    {
        $shelterHistory = $this
            ->getEntityManager()
            ->getRepository('AppBundle:ShelterHistory')
            ->createQueryBuilder('sh')
            ->leftJoin('sh.status', 'shst')
            ->where('sh.client = :client')
            ->andWhere('shst.syncId = :status')
            ->orderBy('sh.dateFrom', 'desc')
            ->addOrderBy('sh.id', 'DESC')
            ->setParameters(['client' => $client, 'status' => ShelterStatus::IN_PROCESS])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $notices = [];

        if (!$shelterHistory instanceof ShelterHistory) {
            return $notices;
        }

        if (!$shelterHistory->getFluorographyDate() instanceof \DateTime) {
            $notices[] = 'Отсутствует дата прохождения флюорографии';
        } elseif ($shelterHistory->getFluorographyDate()->diff(new \DateTime())->days > 180) {
            $notices[] = 'С даты прохождения флюорографии прошло более 180 дней';
        }

        if (!$shelterHistory->getDiphtheriaVaccinationDate() instanceof \DateTime) {
            $notices[] = 'Отсутствует дата прививки от дифтерии';
        }

        if (!$shelterHistory->getHepatitisVaccinationDate() instanceof \DateTime) {
            $notices[] = 'Отсутствует дата прививки от гепатита';
        }

        if (!$shelterHistory->getTyphusVaccinationDate() instanceof \DateTime) {
            $notices[] = 'Отсутствует дата прививки от тифа';
        }

        return $notices;
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
            ->setParameters(array(
                'contractCreatedBy' => $filter['contractCreatedBy'],
                'contractStatus' => $filter['contractStatus']
            ))
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
}
