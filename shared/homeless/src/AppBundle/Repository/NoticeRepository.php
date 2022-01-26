<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponseValue;
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
     * @param $clientFormsEnabled
     * @return int
     */
    public function getMyClientsNoticeHeaderCount($filter, User $user, $clientFormsEnabled)
    {
        return count($this->getMyClientsNoticeHeader($filter, $user, $clientFormsEnabled));
    }

    /**
     * @param $filter
     * @param User $user
     * @param $clientFormsEnabled
     * @return array
     */
    public function getMyClientsNoticeHeader($filter, User $user, $clientFormsEnabled)
    {
        $isNotification = $this
            ->getEntityManager()
            ->getRepository(MenuItem::class)
            ->isEnableCode(MenuItem::CODE_NOTIFICATIONS);
        if (!$isNotification) {
            return  [];
        }
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

        $isQuestionnaireLiving = $this
            ->getEntityManager()
            ->getRepository(MenuItem::class)
            ->isEnableCode(MenuItem::CODE_QUESTIONNAIRE_LIVING);
        if ($isQuestionnaireLiving) {
            $sql = null;
            $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
            $qnrType3Mon = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS;
            $qnrType6Mon = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS;
            $qnrType1Year = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR;
            $qnrType2Years = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS;
            $qnrTypeLeaving = ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_WHEN_LEAVING;
            $maxResFormTTL = 9999;
            if (!$clientFormsEnabled) {
                $sql = "SELECT cl.*
                FROM client cl
                JOIN (SELECT MAX(id) id, client_id FROM contract GROUP BY client_id) ct ON ct.client_id= cl.id
                JOIN contract c on c.id = ct.id
                JOIN (SELECT MAX(id) id, created_by_id AS shelter_created_by, updated_by_id AS shelter_updated_by, client_id, MAX(date_to) date_to, MAX(date_from) date_from FROM shelter_history WHERE date_to IS NOT NULL GROUP BY client_id) sh ON sh.client_id= c.client_id
                LEFT JOIN resident_questionnaire rq3 ON rq3.client_id = c.client_id AND rq3.type_id = 1
                LEFT JOIN resident_questionnaire rq6 ON rq6.client_id = c.client_id AND rq6.type_id = 2
                LEFT JOIN resident_questionnaire rq12 ON rq12.client_id = c.client_id AND rq12.type_id = 3
                WHERE c.created_by_id = ? AND (
                        (rq3.id IS NULL AND rq6.id IS NULL AND rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 3 MONTH) < NOW()) OR
                        (rq6.id IS NULL AND rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 6 MONTH) < NOW()) OR
                        (rq12.id IS NULL AND DATE_ADD(sh.date_to, INTERVAL 12 MONTH) < NOW())
                    ) AND sh.date_to >= '2019-01-01';";
            } else {
                $residentFormsSubquery = "
                    SELECT frv.client_id,
                        MAX(CASE frv.value
                            -- для каждого типа анкеты указано, когда нужно напомнить о заполнении следующей
                            WHEN '$qnrTypeLeaving' THEN 3
                            WHEN '$qnrType3Mon' THEN 6
                            WHEN '$qnrType6Mon' THEN 12
                            WHEN '$qnrType1Year' THEN 24
                            -- если возвращается $maxResFormTTL, то больше не будет напоминаний
                            WHEN '$qnrType2Years' THEN $maxResFormTTL
                            ELSE 0
                            END
                        ) max_ttl_months,
                        frv.created_by_id
                    FROM client_form_response_value frv
                    WHERE frv.client_form_field_id = $qnrTypeFieldId
                    GROUP BY frv.client_id
                ";
                $sql = "SELECT cl.*
                    FROM client cl
                    JOIN (SELECT MAX(id) id, client_id FROM contract GROUP BY client_id) ct ON ct.client_id= cl.id
                    JOIN contract c on c.id = ct.id
                    JOIN (SELECT MAX(id) id, created_by_id AS shelter_created_by, updated_by_id AS shelter_updated_by, client_id, MAX(date_to) date_to, MAX(date_from) date_from FROM shelter_history WHERE date_to IS NOT NULL GROUP BY client_id) sh ON sh.client_id= c.client_id
                    LEFT JOIN ($residentFormsSubquery) res_forms ON res_forms.client_id = c.client_id
                    WHERE (sh.shelter_created_by = ? OR sh.shelter_updated_by = ?)
                        -- у клиента нет ни одной заполненной анкеты, или есть, но не с максимально возможным типом
                        AND (res_forms.max_ttl_months IS NULL OR res_forms.max_ttl_months != $maxResFormTTL)
                        -- по максимальному сроку анкеты понятно, что уже пора заполнять за следующий период
                        AND DATE_ADD(sh.date_to, INTERVAL IFNULL(res_forms.max_ttl_months, 0) MONTH) < NOW()
                        AND sh.date_to >= '2019-01-01';";
            }

            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
            $rsm->addRootEntityFromClassMetadata(Client::class, 'c');

            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            if ($clientFormsEnabled) {
                $query->setParameter(2, $user->getId());
            } else {
                $query->setParameter(1, $user->getId());
            }

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
                        'clientFormsEnabled' => $clientFormsEnabled,
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
        $isNotification = $this
            ->getEntityManager()
            ->getRepository(MenuItem::class)
            ->isEnableCode(MenuItem::CODE_NOTIFICATIONS);
        if (!$isNotification) {
            return  [];
        }
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
