<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ReportService
{
    const ONE_OFF_SERVICES = 'one_off_services';
    // const ONE_OFF_SERVICES_USERS = 'one_off_services_users';
    const COMPLETED_ITEMS = 'completed_items';
    // const COMPLETED_ITEMS_USERS = 'completed_items_users';
    const OUTGOING = 'outgoing';
    const RESULTS_OF_SUPPORT = 'results_of_support';
    const ACCOMPANYING = 'accompanying';
    const AGGREGATED = 'aggregated';
    const AVERAGE_COMPLETED_ITEMS = 'average_completed_items';
    const AGGREGATED2 = 'aggregated2';

    private $em;
    private $doc;

    /**
     * CertificateRecreator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->doc = new \PHPExcel();
    }

    public function getTypes()
    {
        return [
            static::ONE_OFF_SERVICES => 'Отчет о предоставленных разовых услугах',
            // static::ONE_OFF_SERVICES_USERS => 'Отчет о предоставленных разовых услугах по сотрудникам',
            static::COMPLETED_ITEMS => 'Отчет о выполненных пунктах сервисного плана',
            // static::COMPLETED_ITEMS_USERS => 'Отчет о выполненных пунктах сервисного плана по сотрудникам',
            static::OUTGOING => 'Отчет о выбывших из приюта',
            static::RESULTS_OF_SUPPORT => 'Отчет по результатам сопровождения ',
            static::ACCOMPANYING => 'Отчет по сопровождению',
            static::AVERAGE_COMPLETED_ITEMS => 'Отчет по средней длительности пунктов сервисных планов',
            static::AGGREGATED => 'Отчет агрегированный',
            static::AGGREGATED2 => 'Отчет агрегированный 2',
        ];
    }

    /**
     * @param $type
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @param null $createClientdateFrom
     * @param null $createClientFromTo
     * @param null $createServicedateFrom
     * @param null $createServiceFromTo
     * @param null $homelessReason
     * @param null $disease
     * @param null $breadwinner
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function generate(
        $type,
        $dateFrom = null,
        $dateTo = null,
        $userId = null,
        $createClientdateFrom = null,
        $createClientFromTo = null,
        $createServicedateFrom = null,
        $createServiceFromTo = null,
        $homelessReason = null,
        $disease = null,
        $breadwinner = null,
        $branchId = null
    ) {
        if ($dateFrom) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($dateFrom));
            $dateFrom = $date->format('Y-m-d');
        }

        if ($dateTo) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($dateTo));
            $dateTo = $date->format('Y-m-d');
        }

        $result = [];
        $this->doc->setActiveSheetIndex(0);
        switch ($type) {
            case static::ONE_OFF_SERVICES:
                $result = $this->oneOffServices($dateFrom, $dateTo, $userId, $branchId);
                break;

                // case static::ONE_OFF_SERVICES_USERS:
                //     $result = $this->oneOffServicesUsers($dateFrom, $dateTo, $userId, $branchId);
                //     break;

            case static::COMPLETED_ITEMS:
                $result = $this->completedItems($dateFrom, $dateTo, $userId, $branchId);
                break;

                // case static::COMPLETED_ITEMS_USERS:
                //     $result = $this->completedItemsUsers($dateFrom, $dateTo, $userId, $branchId);
                //     break;

            case static::OUTGOING:
                $result = $this->outgoing($dateFrom, $dateTo, $userId, $branchId);
                break;

            case static::RESULTS_OF_SUPPORT:
                $result = $this->resultsOfSupport($dateFrom, $dateTo, $userId, $branchId);
                break;

            case static::ACCOMPANYING:
                $result = $this->accompanying($userId, $branchId);
                break;
            case static::AVERAGE_COMPLETED_ITEMS:
                $result = $this->averageCompletedItems($dateFrom, $dateTo, $userId, $branchId);
                break;

            case static::AGGREGATED:
                $result = $this->aggregated($createClientdateFrom, $createClientFromTo, $createServicedateFrom, $createServiceFromTo, $branchId);
                break;

            case static::AGGREGATED2:
                $result = $this->aggregated2($createClientdateFrom, $createClientFromTo, $createServicedateFrom, $createServiceFromTo, $homelessReason, $disease, $breadwinner, $branchId);
                break;
        }

        $this->doc->getActiveSheet()->fromArray($result, null, 'A2');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $type . '.xls"');
        header('Cache-Control: max-age=0');
        $writer = \PHPExcel_IOFactory::createWriter($this->doc, 'Excel5');
        $writer->save('php://output');
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \PHPExcel_Exception
     */
    private function oneOffServices($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    {

        $fields = [
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $query = $this->em->createQuery('SELECT st.name, COUNT(DISTINCT s.id) all_count, COUNT(DISTINCT s.client) client_count
            FROM AppBundle\Entity\Service s
            JOIN s.type st
            JOIN s.createdBy cb
            JOIN cb.branch cbb
            WHERE s.createdAt >= :dateFrom AND s.createdAt <= :dateTo ' . ($userId ? 'AND s.createdBy IN (' . implode(',', $userId) . ')' : '') . '  ' . ($branchId ? 'AND cb.branch = :branchId' : '') . '
            GROUP BY s.type
            ORDER BY st.sort');
        $parameters = [
            'dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];


        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }
        $query->setParameters($parameters);
        return $query->getResult();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    // private function oneOffServicesUsers($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    // {

    //     $fields = [
    //         'ФИО сотрудникa',
    //         'название услуги',
    //         'сколько раз она была предоставлена'
    //     ];


    //     $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


    //     $stmt = $this->em->getConnection()->prepare('SELECT concat(u.lastname, \' \', u.firstname, \' \', u.middlename), st.name, COUNT(DISTINCT s.id) count
    //         FROM service s
    //         JOIN service_type st ON s.type_id = st.id
    //         LEFT JOIN fos_user_user u ON s.created_by_id = u.id
    //         LEFT JOIN branches AS b ON b.id = u.branch_id
    //         WHERE s.created_at >= :dateFrom AND s.created_at <= :dateTo ' . ($userId ? 'AND s.created_by_id = :userId' : '') . ' ' .  ($branchId ? 'AND u.branch_id = :branchId' : '') . '
    //         GROUP BY u.id, s.type_id
    //         ORDER BY st.sort');
    //     $parameters = [
    //         'dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
    //         'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
    //     ];
    //     if ($userId) {
    //         $parameters['userId'] = $userId;
    //     }

    //     if ($branchId) {
    //         $parameters['branchId'] = $branchId;
    //     }

    //     $stmt->execute($parameters);
    //     return $stmt->fetchAll();
    // }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function completedItems($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    {

        $fields = [
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $stmt = $this->em->getConnection()->prepare('SELECT cit.name, COUNT(DISTINCT i.id) all_count, COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            LEFT JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')) OR (i.created_by_id IS NULL AND c.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')))' : '') . '
            ' . ($branchId ? 'AND u.branch_id = :branchId' : '') . '
            GROUP BY i.type_id
            ORDER BY cit.sort');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        // if ($userId) {
        //     $parameters[':userId'] = $userId;
        // }

        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }


        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    // private function completedItemsUsers($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    // {
    //     $fields = [
    //         'ФИО сотрудника',
    //         'название пункта',
    //         'сколько раз он был выполнен'
    //     ];



    //     $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


    //     $stmt = $this->em->getConnection()->prepare('SELECT concat(u.lastname, \' \', u.firstname, \' \', u.middlename) full_name, cit.name, COUNT(*) count
    //         FROM contract_item i
    //           JOIN contract c ON i.contract_id = c.id
    //           JOIN contract_item_type cit ON i.type_id = cit.id
    //           LEFT JOIN fos_user_user u ON (i.created_by_id IS NOT NULL AND i.created_by_id = u.id) OR (i.created_by_id IS NULL AND c.created_by_id = u.id)
    //           LEFT JOIN branches AS b ON b.id = u.branch_id
    //         WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND u.id = :userId' : '') . ' ' .  ($branchId ? 'AND u.branch_id = :branchId' : '')  . '
    //         GROUP BY i.type_id, u.id
    //         ORDER BY i.id, cit.sort');
    //     $parameters = [
    //         ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
    //         ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
    //     ];
    //     if ($userId) {
    //         $parameters[':userId'] = $userId;
    //     }

    //     if ($branchId) {
    //         $parameters['branchId'] = $branchId;
    //     }

    //     $stmt->execute($parameters);
    //     return $stmt->fetchAll();
    // }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function outgoing($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    {
        $fields = [
            'ID',
            'ФИО',
            'Дата заселения',
            'Дата выселения',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана на момент выселения',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $stmt = $this->em->getConnection()->prepare('SELECT c.id, concat(c.lastname, \' \', c.firstname, \' \', c.middlename), h.date_from, h.date_to, GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')), GROUP_CONCAT(CONCAT(cit2.name, \'(\' , ci2.comment, \')\')), cs.name, con.comment, concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
            JOIN shelter_history h ON con.id = h.contract_id
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN contract_item ci1 ON con.id = ci1.contract_id AND ci1.date IS NOT NULL
            LEFT JOIN contract_item_type cit1 ON ci1.type_id = cit1.id
            LEFT JOIN contract_item ci2 ON con.id = ci2.contract_id AND ci2.date IS NULL
            LEFT JOIN contract_item_type cit2 ON ci2.type_id = cit2.id
            JOIN contract_status cs ON con.status_id = cs.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE h.date_to >= :dateFrom AND h.date_to <= :dateTo ' . ($userId ? 'AND u.id IN (' . implode(',', array_map('intval', $userId)) . ')' : '') . ' ' . ($branchId ? 'AND u.branch_id = :branchId' : '')  . '
            GROUP BY con.id
            ORDER BY h.date_to DESC');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        // if ($userId) {
        //     $parameters[':userId'] = $userId;
        // }

        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }

        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function resultsOfSupport($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    {
        $fields = [
            'ID',
            'ФИО',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');

        $stmt = $this->em->getConnection()->prepare('SELECT c.id, concat(c.lastname, \' \', c.firstname, \' \', c.middlename), GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')), GROUP_CONCAT(CONCAT(cit2.name, \'(\' , ci2.comment, \')\')), cs.name, con.comment, concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN contract_item ci1 ON con.id = ci1.contract_id AND ci1.date IS NOT NULL
            LEFT JOIN contract_item_type cit1 ON ci1.type_id = cit1.id
            LEFT JOIN contract_item ci2 ON con.id = ci2.contract_id AND ci2.date IS NULL
            LEFT JOIN contract_item_type cit2 ON ci2.type_id = cit2.id
            JOIN contract_status cs ON con.status_id = cs.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE con.date_to >= :dateFrom AND con.date_to <= :dateTo ' . ($userId ? 'AND u.id IN (' . implode(',', array_map('intval', $userId)) . ')' : '') . '
                AND con.status_id <> 1 ' . ($branchId ? 'AND u.branch_id = :branchId' : '') . '
            GROUP BY con.id
            ORDER BY con.date_to DESC');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        // if ($userId) {
        //     $parameters[':userId'] = $userId;
        // }

        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }


        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function accompanying($userId = null, $branchId = null)
    {
        $fields = [
            'ID',
            'ФИО',
            'пункты сервисного плана с комментариями',
            'статус',
            'комментарии к сервисному плану в целом',
            'длительность выполнения',
            'ФИО соцработника, открывшего сервисный план',
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $stmt = $this->em->getConnection()->prepare('SELECT
              c.id,
              concat(c.lastname, \' \', c.firstname, \' \', c.middlename),
              GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')),
              cs.name,
              con.comment,
              TO_DAYS(ci1.date) - TO_DAYS(ci1.date_start),
              concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN contract_item ci1 ON con.id = ci1.contract_id
            LEFT JOIN contract_item_type cit1 ON ci1.type_id = cit1.id
            JOIN contract_status cs ON con.status_id = cs.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE ' . ($userId ? ' u.id IN (' . implode(',', array_map('intval', $userId)) . ') AND ' : '') . '
                status_id = 1 ' .  ($branchId ? 'AND u.branch_id = :branchId' : '') . '
            GROUP BY con.id
            ORDER BY con.date_to DESC');
        $parameters = [];

        // if ($userId) {
        //     $parameters[':userId'] = $userId;
        // }

        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function averageCompletedItems($dateFrom = null, $dateTo = null, $userId = null, $branchId = null)
    {

        $fields = [
            'название пункта',
            'средняя длительность',
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $stmt = $this->em->getConnection()->prepare('SELECT
                cit.name,
                FLOOR(AVG (TO_DAYS(c.date_to) - TO_DAYS(c.date_from))) avg_days
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')) OR (i.created_by_id IS NULL AND c.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')))' : '') . '
            ' .   ($branchId ? 'AND u.branch_id = :branchId' : '') . '
            GROUP BY cit.name
            ORDER BY cit.name');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        // if ($userId) {
        //     $parameters[':userId'] = $userId;
        // }
        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }

        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * @param null $createClientdateFrom
     * @param null $createClientFromTo
     * @param null $createServicedateFrom
     * @param null $createServiceFromTo
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function aggregated($createClientdateFrom = null, $createClientFromTo = null, $createServicedateFrom = null, $createServiceFromTo = null, $branchId = null)
    {

        $fields = [
            'Вопрос',
            'Ответ',
            'Количество',
        ];

        $branchName = 'Все';
        if (!is_null($branchId)) {
            $branchName = $branchId == '1' ? 'Санкт-Петербург' : ($branchId == '0' ? 'Все' : 'Москва');
            $fields[] = 'отделение';
        }

        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');

        $clientsIds = null;
        if ($createServicedateFrom || $createServiceFromTo) {
            // $stmt = $this->em->getConnection()->prepare('SELECT DISTINCT c.id
            // FROM client c
            // JOIN service s ON s.client_id = c.id
            // JOIN fos_user_user u ON c.created_by_id = u.id
            // LEFT JOIN branches AS b ON b.id = u.branch_id
            // WHERE s.created_at >= :createServicedateFrom AND s.created_at <= :createServiceFromTo AND
            //       c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo ' .  ($branchId ? 'AND u.branch_id = :branchId' : '')
            // );


            $stmt = $this->em->getConnection()->prepare('SELECT DISTINCT s.`client_id` as `id`
            FROM `service` AS s
            JOIN `service_type` AS st ON st.`id` = s.`type_id`
            JOIN `client` AS c ON c.`id` = s.`client_id`
            JOIN `fos_user_user` AS u ON u.`id` = s.`created_by_id`
            JOIN `branches` AS b ON b.`id` = u.`branch_id`
            WHERE s.`created_at` >= :createServicedateFrom AND s.`created_at` <= :createServiceFromTo AND c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo ' . ($branchId ? 'AND u.`branch_id` = :branchId' : '') . '
            ORDER BY st.`sort`');

            $parameters = [
                ':createServicedateFrom' => $createServicedateFrom ? date('Y-m-d', strtotime($createServicedateFrom)) : '1960-01-01',
                ':createServiceFromTo' => $createServiceFromTo ? date('Y-m-d', strtotime($createServiceFromTo)) : date('Y-m-d'),
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        } else {
            $stmt = $this->em->getConnection()->prepare('SELECT DISTINCT s.`client_id` AS `id`
            FROM `service` AS s
            JOIN `service_type` AS st ON st.`id` = s.`type_id`
            JOIN `client` AS c ON c.`id` = s.`client_id`
            JOIN `fos_user_user` AS u ON u.`id` = s.`created_by_id`
            JOIN `branches` AS b ON b.`id` = u.`branch_id`
            WHERE c.`created_at` >= :createClientdateFrom AND c.`created_at` <= :createClientFromTo ' . ($branchId ? 'AND u.`branch_id` = :branchId' : '') . '
            ORDER BY st.`sort`');

            $parameters = [
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        }

        if ($branchId) {
            $parameters[':branchId'] = $branchId;
        }

        $stmt->execute($parameters);
        $clientsIds = [];


        foreach ($stmt->fetchAll() as $item) {
            $clientsIds[] = $item['id'];
        }

        $clientsIds = array_unique($clientsIds);
        if (!$clientsIds) {
            return [];
        }
        $stmt = $this->em->getConnection()->prepare('(
            SELECT \'Количество\', \'Общее\', COUNT(*), \'' . $branchName . '\'
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ')
            )
            union
            (
            SELECT \'Количество\', \'Мужчин\', COUNT(*), \'' . $branchName . '\'
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ') AND c.gender = 1
            )
            union
            (
            SELECT \'Количество\', \'Женщин\', COUNT(*), \'' . $branchName . '\'
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ') AND c.gender = 2
            )
            union
            (
            SELECT \'Средний\', \'Возраст\', CAST(AVG(TIMESTAMPDIFF(YEAR,c.birth_date,curdate())) AS UNSIGNED), \'' . $branchName . '\'
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ')
            )
            union
            (
            SELECT cf.name, \'Есть\', COUNT(*), \'' . $branchName . '\'
            FROM client c
            JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
            JOIN client_field cf ON cfv.field_id = cf.id
            JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
            JOIN client_field_option cfo on cfvcfo.client_field_option_id = cfo.id
            WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code = \'profession\'
            )
            union
            (
            SELECT \'Профессия\', \'Нет\', ((
            SELECT COUNT(*)
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ')
            ) - (
            SELECT COUNT(*)
            FROM client c
            JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
            JOIN client_field cf ON cfv.field_id = cf.id
            JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
            JOIN client_field_option cfo on cfvcfo.client_field_option_id = cfo.id
            WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code = \'profession\'
            )), \'' . $branchName . '\'
            )
            union
            (
            SELECT cf.name, cfo.name, COUNT(*), \'' . $branchName . '\'
            FROM client c
            JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
            JOIN client_field cf ON cfv.field_id = cf.id
            JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
            JOIN client_field_option cfo on cfvcfo.client_field_option_id = cfo.id
            WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code != \'profession\'
            GROUP BY cf.name
                , cfo.name
            )
            union
            (
            SELECT cf.name, cfo.name, COUNT(*), \'' . $branchName . '\'
            FROM client c
            JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NOT NULL
            JOIN client_field cf ON cfv.field_id = cf.id
            JOIN client_field_option cfo on cfv.option_id = cfo.id
            WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code != \'profession\'
            GROUP BY cf.name
                , cfo.name
            )');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param null $createClientdateFrom
     * @param null $createClientFromTo
     * @param null $createServicedateFrom
     * @param null $createServiceFromTo
     * @param null $homelessReason
     * @param null $disease
     * @param null $breadwinner
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function aggregated2($createClientdateFrom = null, $createClientFromTo = null, $createServicedateFrom = null, $createServiceFromTo = null, $homelessReason = null, $disease = null, $breadwinner = null, $branchId = null)
    {
        $fields = [
            'Количество',
        ];

        if(!is_null($branchId)) {
            $branchName = $branchId == '1' ? 'Санкт-Петербург' : ($branchId == '0' ? 'Все' : 'Москва');
            $fields[] = 'отделение';
        }

        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        if ($createServicedateFrom || $createServiceFromTo) {
            $stmt = $this->em->getConnection()->prepare(
                'SELECT c.id
            FROM client c
            JOIN service s ON s.client_id = c.id
            JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE s.created_at >= :createServicedateFrom AND s.created_at <= :createServiceFromTo AND
                  c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo ' .  ($branchId ? 'AND u.branch_id = :branchId' : '')
            );
            $parameters = [
                ':createServicedateFrom' => $createServicedateFrom ? date('Y-m-d', strtotime($createServicedateFrom)) : '1960-01-01',
                ':createServiceFromTo' => $createServiceFromTo ? date('Y-m-d', strtotime($createServiceFromTo)) : date('Y-m-d'),
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        } else {
            $stmt = $this->em->getConnection()->prepare(
                'SELECT c.id
            FROM client c
            JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo ' .  ($branchId ? 'AND u.branch_id = :branchId' : '')
            );
            $parameters = [
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        }


        if ($branchId) {
            $parameters[':branchId'] = $branchId;
        }

        $stmt->execute($parameters);
        $clientsIds = [];
        foreach ($stmt->fetchAll() as $item) {
            $clientsIds[$item['id']] = 0;
        }

        if (!$clientsIds) {
            return [];
        }

        if ($homelessReason || $disease || $breadwinner) {
            if ($disease) {
                $stmt = $this->em->getConnection()->prepare('SELECT c.id
                    FROM client c
                    LEFT JOIN client_field_value cfv ON c.id = cfv.client_id
                    LEFT JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
                    LEFT JOIN client_field cf ON cfv.field_id = cf.id
                    WHERE cf.code = \'disease\' AND cfvcfo.client_field_option_id IN (' . implode(',', $disease) . ')');

                $stmt->execute();
                foreach ($stmt->fetchAll() as $item) {
                    if (!isset($clientsIds[$item['id']])) {
                        continue;
                    }
                    $clientsIds[$item['id']]++;
                }
            }
            if ($homelessReason) {
                $stmt = $this->em->getConnection()->prepare('SELECT c.id
                    FROM client c
                    LEFT JOIN client_field_value cfv ON c.id = cfv.client_id
                    LEFT JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
                    LEFT JOIN client_field cf ON cfv.field_id = cf.id
                    WHERE cf.code = \'homelessReason\' AND cfvcfo.client_field_option_id IN (' . implode(',', $homelessReason) . ')');
                $stmt->execute();
                foreach ($stmt->fetchAll() as $item) {
                    if (!isset($clientsIds[$item['id']])) {
                        continue;
                    }
                    $clientsIds[$item['id']]++;
                }
            }
            if ($breadwinner) {
                $stmt = $this->em->getConnection()->prepare('SELECT c.id
                    FROM client c
                    LEFT JOIN client_field_value cfv ON c.id = cfv.client_id
                    LEFT JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
                    LEFT JOIN client_field cf ON cfv.field_id = cf.id
                    WHERE cf.code = \'breadwinner\' AND cfvcfo.client_field_option_id IN (' . implode(',', $breadwinner) . ')');
                $stmt->execute();
                foreach ($stmt->fetchAll() as $item) {
                    if (!isset($clientsIds[$item['id']])) {
                        continue;
                    }
                    $clientsIds[$item['id']]++;
                }
            }

            $max = max($clientsIds);

            foreach ($clientsIds as $clientsId => $value) {
                if ($value !== $max) {
                    unset($clientsIds[$clientsId]);
                }
            }

            $clientsIds = array_unique(array_keys($clientsIds));
        }

        if ($clientsIds === []) {
            return [];
        }
        $stmt = $this->em->getConnection()->prepare('
            SELECT COUNT(*), \''.$branchName.'\'
            FROM client c
            WHERE c.id IN (' . implode(',', $clientsIds) . ')');

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
