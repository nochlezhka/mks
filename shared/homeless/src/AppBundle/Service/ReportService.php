<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\DeliveryItem;
use AppBundle\Entity\ShelterHistory;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class ReportService
{
    const ONE_OFF_SERVICES = 'one_off_services';
    const COMPLETED_ITEMS = 'completed_items';
    const OUTGOING = 'outgoing';
    const RESULTS_OF_SUPPORT = 'results_of_support';
    const ACCOMPANYING = 'accompanying';
    const AGGREGATED = 'aggregated';
    const AVERAGE_COMPLETED_ITEMS = 'average_completed_items';
    const AGGREGATED2 = 'aggregated2';
    const DELIVERY = 'delivery';
    const SHELTERREPORT = 'shelter_report';

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
            static::COMPLETED_ITEMS => 'Отчет о выполненных пунктах сервисного плана',
            static::OUTGOING => 'Отчет о выбывших из приюта',
            static::RESULTS_OF_SUPPORT => 'Отчет по результатам сопровождения ',
            static::ACCOMPANYING => 'Отчет по сопровождению',
            static::AVERAGE_COMPLETED_ITEMS => 'Отчет по средней длительности пунктов сервисных планов',
            static::AGGREGATED => 'Отчет агрегированный',
            static::AGGREGATED2 => 'Отчет агрегированный 2',
            static::DELIVERY => 'Отчет о выдаче',
            static::SHELTERREPORT => 'Отчет о выбывших по анкетам'
        ];
    }

    public function merge_results($arr1, $arr2, $large)
    {
        $res = [];
        if (!empty($arr1) && !empty($arr2)) {
            $checkedItems = [];
            // $checkedItemsIndexes = [];
            foreach ($arr1 as $index => $arEl) {
                if (!in_array($arEl['name'], $checkedItems)) {
                    $res[$index] = [
                        'name' => $arEl['name'],
                        'all_count_msk' => ($large == 'spb') ? '0' : $arEl['all_count'],
                        'client_count_msk' => ($large == 'spb') ? '0' : $arEl['client_count'],
                        'all_count_spb' => ($large == 'spb') ? $arEl['all_count'] : '0',
                        'client_count_spb' => ($large == 'spb') ? $arEl['client_count'] : '0',
                    ];

                    $checkedItems[] = $arEl['name'];
                    // $checkedItemsIndexes[$arEl['name']] = $index;

                }
            }

            $all_count = $large == 'spb' ? 'all_count_msk' : 'all_count_spb';
            $client_count = $large == 'spb' ? 'client_count_msk' : 'client_count_spb';

            foreach ($arr2 as $arEl) {
                if (in_array($arEl['name'], $checkedItems)) {
                    $idx = array_search($arEl['name'], $checkedItems);

                    $res[$idx][$all_count] = $arEl['all_count'];
                    $res[$idx][$client_count] = $arEl['client_count'];
                } else {
                    $res[] = [
                        'name' => $arEl['name'],
                        'all_count_msk' => ($large == 'spb') ? $arEl['all_count'] : '0',
                        'client_count_msk' => ($large == 'spb') ? $arEl['client_count'] : '0',
                        'all_count_spb' => ($large == 'spb') ? '0' : $arEl['all_count'],
                        'client_count_spb' => ($large == 'spb') ? '0' : $arEl['client_count'],
                    ];

                    $checkedItems[] = $arEl['name'];
                }
            }
        }

        return $res;
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
     * @param null $serviceTypeId
     * @param null $contractTypeId
     * @param null $deliveryItemId
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
        $branchId = null,
        $serviceTypeId = null,
        $contractTypeId = null,
        $deliveryItemId = null
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
                $result = $this->oneOffServices($dateFrom, $dateTo, $userId, $branchId, $serviceTypeId);
                break;

            case static::COMPLETED_ITEMS:
                $result = $this->completedItems($dateFrom, $dateTo, $userId, $branchId, $contractTypeId);
                break;

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

            case static::DELIVERY:
                $result = $this->delivery($dateFrom, $dateTo, $userId, $branchId, $deliveryItemId);
                break;

            case static::SHELTERREPORT:
                $result = $this->shelterReport($dateFrom, $dateTo, $userId);
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
     * @param null $branchId
     * @param null $servicesArray
     * @return array
     * @throws \PHPExcel_Exception
     */
    private function oneOffServices($dateFrom = null, $dateTo = null, $userId = null, $branchId = null, $servicesArray = null)
    {

        $fields = [
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ];

        if (!$branchId) {


            $fields[1] = 'Предоставлена раз (Санкт-Петербург)';
            $fields[2] = 'Кол-во людей (Санкт-Петербург)';
            $fields[3] = 'Предоставлена мужчинам раз (Санкт-Петербург)';
            $fields[4] = 'Кол-во мужчин (Санкт-Петербург)';
            $fields[5] = 'Предоставлена женщинам раз (Санкт-Петербург)';
            $fields[6] = 'Кол-во женщин (Санкт-Петербург)';
            $fields[7] = 'Предоставлена раз (Москва)';
            $fields[8] = 'Кол-во людей (Москва)';
            $fields[9] = 'Предоставлена мужчинам раз (Москва)';
            $fields[10] = 'Кол-во мужчин (Москва)';
            $fields[11] = 'Предоставлена женщинам раз (Москва)';
            $fields[12] = 'Кол-во женщин (Москва)';
        } else {
            $branchName = $branchId == '1' ? 'Санкт-Петербург' : 'Москва';

            $fields[1] = 'Предоставлена раз (' . $branchName . ')';
            $fields[2] = 'Кол-во людей (' . $branchName . ')';
            $fields[3] = 'Предоставлена мужчинам раз (' . $branchName . ')';
            $fields[4] = 'Кол-во мужчин (' . $branchName . ')';
            $fields[5] = 'Предоставлена женщинам раз (' . $branchName . ')';
            $fields[6] = 'Кол-во женщин (' . $branchName . ')';
        }


        $servicesSql = '';
        if (!empty($servicesArray)) {
            $servicesSql = implode(', ', $servicesArray);
        }


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');

        $rsm = new ResultSetMappingBuilder($this->em);

        if (!$branchId) {

            $rsm->addScalarResult('name', 'name');

            $rsm->addScalarResult('spb_all_count', 'spb_all_count');
            $rsm->addScalarResult('spb_client_count', 'spb_client_count');
            $rsm->addScalarResult('spb_all_male_count', 'spb_all_male_count');
            $rsm->addScalarResult('spb_client_male_count', 'spb_client_male_count');
            $rsm->addScalarResult('spb_all_female_count', 'spb_all_female_count');
            $rsm->addScalarResult('spb_client_female_count', 'spb_client_female_count');

            $rsm->addScalarResult('msk_all_count', 'msk_all_count');
            $rsm->addScalarResult('msk_client_count', 'msk_client_count');
            $rsm->addScalarResult('msk_all_male_count', 'msk_all_male_count');
            $rsm->addScalarResult('msk_client_male_count', 'msk_client_male_count');
            $rsm->addScalarResult('msk_all_female_count', 'msk_all_female_count');
            $rsm->addScalarResult('msk_client_female_count', 'msk_client_female_count');

            $query = $this->em->createNativeQuery('

            SELECT src.name,
                SUM(IF(src.branch_id = 1,src.all_count,0)) AS spb_all_count,
                SUM(IF(src.branch_id = 1,src.client_count,0)) AS spb_client_count,
                SUM(IF(src.gender = 1 AND src.branch_id = 1,src.all_count,0)) AS spb_all_male_count,
                SUM(IF(src.gender = 1 AND src.branch_id = 1,src.client_count,0)) AS spb_client_male_count,
                SUM(IF(src.gender = 2 AND src.branch_id = 1,src.all_count,0)) AS spb_all_female_count,
                SUM(IF(src.gender = 2 AND src.branch_id = 1,src.client_count,0)) AS spb_client_female_count,
                SUM(IF(src.branch_id = 2,src.all_count,0)) AS msk_all_count,
                SUM(IF(src.branch_id = 2,src.client_count,0)) AS msk_client_count,
                SUM(IF(src.gender = 1 AND src.branch_id = 2,src.all_count,0)) AS msk_all_male_count,
                SUM(IF(src.gender = 1 AND src.branch_id = 2,src.client_count,0)) AS msk_client_male_count,
                SUM(IF(src.gender = 2 AND src.branch_id = 2,src.all_count,0)) AS msk_all_female_count,
                SUM(IF(src.gender = 2 AND src.branch_id = 2,src.client_count,0)) AS msk_client_female_count,
                src.type
            FROM
                (SELECT st.name,s.type_id AS type,c.gender,COUNT(DISTINCT s.id) all_count,COUNT(DISTINCT s.client_id) client_count,cbb.id branch_id
                    FROM service AS s
                    INNER JOIN client AS c ON c.id = s.client_id
                    INNER JOIN service_type AS st ON st.id = s.type_id
                    INNER JOIN fos_user_user AS cb ON cb.id = s.created_by_id
                    INNER JOIN branches AS cbb ON cbb.id = cb.branch_id
                    WHERE s.created_at >= :dateFrom AND s.created_at <= :dateTo '
                        . ($userId ? 'AND s.created_by_id IN (' . implode(',', $userId) . ')' : '') . '  '
                        . (!empty($servicesSql) ? 'AND st.id NOT IN (' . $servicesSql . ')' : '') . '
                    GROUP BY s.type_id,c.gender,cbb.id
                    ORDER BY st.sort
                ) src
            GROUP BY src.type

            ',$rsm);

            $parameters = [
                'dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
                'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
            ];

            $query->setParameters($parameters);
            return $query->getScalarResult();
        } else {
            $rsm->addScalarResult('name', 'name');

            $rsm->addScalarResult('all_count', 'all_count');
            $rsm->addScalarResult('client_count', 'client_count');

            $rsm->addScalarResult('all_male_count', 'all_male_count');
            $rsm->addScalarResult('client_male_count', 'client_male_count');
            $rsm->addScalarResult('all_female_count', 'all_female_count');
            $rsm->addScalarResult('client_female_count', 'client_female_count');

            $query = $this->em->createNativeQuery('

            SELECT src.name,
                SUM(src.all_count) AS all_count,
                SUM(src.client_count) AS client_count,
                SUM(IF(src.gender = 1,src.all_count,0)) AS all_male_count,
                SUM(IF(src.gender = 1,src.client_count,0)) AS client_male_count,
                SUM(IF(src.gender = 2,src.all_count,0)) AS all_female_count,
                SUM(IF(src.gender = 2,src.client_count,0)) AS client_female_count,
                src.type
            FROM

                (SELECT st.name,s.type_id AS type,c.gender,COUNT(DISTINCT s.id) all_count,COUNT(DISTINCT s.client_id) client_count,cbb.id branch_id
                    FROM service AS s
                    INNER JOIN client AS c ON c.id = s.client_id
                    INNER JOIN service_type AS st ON st.id = s.type_id
                    INNER JOIN fos_user_user AS cb ON cb.id = s.created_by_id
                    INNER JOIN branches AS cbb ON cbb.id = cb.branch_id
                    WHERE s.created_at >= :dateFrom AND s.created_at <= :dateTo '
                    . ($userId ? 'AND s.created_by_id IN (' . implode(',', $userId) . ')' : '') . '  '
                    . ($branchId ? 'AND cb.branch_id = :branchId' : '') . ' '
                    . (!empty($servicesSql) ? 'AND st.id NOT IN (' . $servicesSql . ')' : '') . '
                    GROUP BY s.type_id,c.gender,cbb.id
                    ORDER BY st.sort
                ) AS src
                GROUP BY src.type

            ',$rsm);

            $parameters = [
                'dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
                'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
            ];

            if ($branchId) {
                $parameters['branchId'] = $branchId;
            }
            $query->setParameters($parameters);

            return $query->getScalarResult();
        }
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @param null $branchId
     * @param null $contractTypeId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     */
    private function completedItems($dateFrom = null, $dateTo = null, $userId = null, $branchId = null, $contractTypeId = null)
    {

        $fields = [
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ];

        if (!$branchId) {
            $fields[1] = 'Предоставлена раз (Москва)';
            $fields[2] = 'Кол-во людей (Москва)';
            $fields[3] = 'Предоставлена раз (Санкт-Петербург)';
            $fields[4] = 'Кол-во людей (Санкт-Петербург)';
        } else {
            $branchName = $branchId == '1' ? 'Санкт-Петербург' : 'Москва';

            $fields[1] = 'Предоставлена раз (' . $branchName . ')';
            $fields[2] = 'Кол-во людей (' . $branchName . ')';
        }

        $serviceSql = '';
        if (!empty($contractTypeId)) {
            $serviceSql = implode(", ", $contractTypeId);
        }


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        if (!$branchId) {
            $branchId = '2';
            $stmt = $this->em->getConnection()->prepare('SELECT cit.name, COUNT(DISTINCT i.id) all_count, COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            LEFT JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')) OR (i.created_by_id IS NULL AND c.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')))' : '') . '
            ' . ($branchId ? 'AND u.branch_id = :branchId' : '') . ' ' . (!empty($serviceSql) ? 'AND cit.`id` NOT IN (' . $serviceSql . ')' : '') . '
            GROUP BY i.type_id
            ORDER BY cit.sort');
            $parameters = [
                'branchId' => $branchId,
                ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
                ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
            ];

            $stmt->execute($parameters);
            $msk_res = $stmt->fetchAll();

            $branchId = '1';

            $parameters['branchId'] = $branchId;
            $stmt->execute($parameters);
            $spb_res = $stmt->fetchAll();

            $res = (count($spb_res) > count($msk_res)) ? $this->merge_results($spb_res, $msk_res, 'spb') : $this->merge_results($msk_res, $spb_res, 'msk');

            return $res;
        } else {
            $stmt = $this->em->getConnection()->prepare('SELECT cit.name, COUNT(DISTINCT i.id) all_count, COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            LEFT JOIN fos_user_user u ON c.created_by_id = u.id
            LEFT JOIN branches AS b ON b.id = u.branch_id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')) OR (i.created_by_id IS NULL AND c.created_by_id IN (' . implode(',', array_map('intval', $userId)) . ')))' : '') . '
            ' . ($branchId ? 'AND u.branch_id = :branchId' : '') . ' ' . (!empty($serviceSql) ? 'AND cit.`id` NOT IN (' . $serviceSql . ')' : '') . '
            GROUP BY i.type_id
            ORDER BY cit.sort');
            $parameters = [
                ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
                ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
            ];


            if ($branchId) {
                $parameters['branchId'] = $branchId;
            }


            $stmt->execute($parameters);
            return $stmt->fetchAll();
        }
    }

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
            'Итог проживания'
        ];


        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        $stmt = $this->em->getConnection()->prepare('SELECT c.id, concat(c.lastname, \' \', c.firstname, \' \', c.middlename), h.date_from, h.date_to, GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')), GROUP_CONCAT(CONCAT(cit2.name, \'(\' , ci2.comment, \')\')), cs.name, con.comment, concat(u.lastname, \' \', u.firstname, \' \', u.middlename),slr.name AS leaving_reason
            FROM contract con
            JOIN shelter_history h ON con.id = h.contract_id
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN shelter_leaving_reason slr ON slr.id = h.leaving_reason_id
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

        return  $stmt->fetchAll();
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

            $stmt2 = $this->em->getConnection()->prepare('SELECT DISTINCT d.`client_id` AS `d_id`
            FROM `delivery` AS d
            JOIN `fos_user_user` AS u on u.`id` = d.`created_by_id`
            JOIN `client` AS c ON c.`id` = d.`client_id`
            WHERE d.`created_at` >= :createServicedateFrom AND d.`created_at`  <= :createServiceFromTo AND c.`created_at` <= :createClientFromTo AND c.`created_at` >= :createClientdateFrom ' . ($branchId ? 'AND u.`branch_id` = :branchId' : ''));

            $parameters = [
                ':createServicedateFrom' => $createServicedateFrom ? date('Y-m-d', strtotime($createServicedateFrom)) : '1960-01-01',
                ':createServiceFromTo' => $createServiceFromTo ? date('Y-m-d', strtotime($createServiceFromTo)) : date('Y-m-d'),
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        } else {
            // $stmt = $this->em->getConnection()->prepare('SELECT c.id
            // FROM client c
            // JOIN fos_user_user u ON c.created_by_id = u.id
            // LEFT JOIN branches AS b ON b.id = u.branch_id
            // WHERE c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo ' .   ($branchId ? 'AND u.branch_id = :branchId' : '')
            // );
            $stmt = $this->em->getConnection()->prepare('SELECT DISTINCT s.`client_id` AS `id`
            FROM `service` AS s
            JOIN `service_type` AS st ON st.`id` = s.`type_id`
            JOIN `client` AS c ON c.`id` = s.`client_id`
            JOIN `fos_user_user` AS u ON u.`id` = s.`created_by_id`
            JOIN `branches` AS b ON b.`id` = u.`branch_id`
            WHERE c.`created_at` >= :createClientdateFrom AND c.`created_at` <= :createClientFromTo ' . ($branchId ? 'AND u.`branch_id` = :branchId' : '') . '
            ORDER BY st.`sort`');

            $stmt2 = $this->em->getConnection()->prepare('SELECT DISTINCT d.`client_id` AS `d_id`
            FROM `delivery` AS d
            JOIN `fos_user_user` AS u on u.`id` = d.`created_by_id`
            JOIN `client` AS c ON c.`id` = d.`client_id`
            WHERE c.`created_at` <= :createClientFromTo AND c.`created_at` >= :createClientdateFrom ' . ($branchId ? 'AND u.`branch_id` = :branchId' : ''));

            $parameters = [
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        }

        if ($branchId) {
            $parameters[':branchId'] = $branchId;
        }

        $stmt->execute($parameters);
        $stmt2->execute($parameters);


        $clientsIds = [];


        foreach ($stmt->fetchAll() as $item) {
            $clientsIds[] = $item['id'];
        }
        foreach ($stmt2->fetchAll() as $item2) {
            $clientsIds[] = $item2['d_id'];
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

        if (!is_null($branchId)) {
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

        if ($clientsIds === [] || (($homelessReason || $disease || $breadwinner) && empty($max))) {
            return [];
        }

        $stmt = $this->em->getConnection()->prepare('
            SELECT COUNT(*), \'' . $branchName . '\'
            FROM client c
            WHERE c.id IN (' . implode(',', ($homelessReason || $disease || $breadwinner) ? $clientsIds : array_keys($clientsIds)) . ')');

        $stmt->execute();
        return $stmt->fetchAll();
    }



    private function delivery($dateFrom = null, $dateTo = null, $userId = null, $branchId = null, array $deliveryItemId = null)
    {
        $fields = [
            'категория',
            'сколько вещей было выдано',
            'скольким людям было выдано',
            'сколько вещей было выдано (Мужчины)',
            'скольким людям было выдано (Мужчины)',
            'сколько вещей было выдано (Женщины)',
            'скольким людям было выдано (Женщины)',
        ];

        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');

        $serviceSql = '';
        if (!empty($deliveryItemId)) {
            $serviceSql = implode(", ", $deliveryItemId);
        }

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('category', 'category');

        $rsm->addScalarResult('all_count', 'all_count');
        $rsm->addScalarResult('client_count', 'client_count');
        $rsm->addScalarResult('male_count', 'male_count');
        $rsm->addScalarResult('client_male_count', 'client_male_count');
        $rsm->addScalarResult('female_count', 'female_count');
        $rsm->addScalarResult('client_female_count', 'client_female_count');

        $query = $this->em->createNativeQuery('

        SELECT src.category,
            SUM(src.all_count) AS all_count,
            SUM(src.client_count) AS client_count,
            SUM(IF(src.gender = 1,src.all_count,0)) AS male_count,
            SUM(IF(src.gender = 1,src.client_count,0)) AS client_male_count,
            SUM(IF(src.gender = 2,src.all_count,0)) AS female_count,
            SUM(IF(src.gender = 2,src.client_count,0)) AS client_female_count,
            src.id
        FROM
            (SELECT di.category,d.id,c.gender,COUNT(DISTINCT d.id) all_count,COUNT(DISTINCT d.client_id) client_count,di.sort
                FROM delivery AS d
                INNER JOIN client AS c ON c.id = d.client_id
                INNER JOIN delivery_item AS di ON di.id = d.delivery_item_id
                INNER JOIN fos_user_user AS cb ON cb.id = d.created_by_id
                INNER JOIN branches AS cbb ON cbb.id = cb.branch_id
                WHERE d.delivered_at >= :dateFrom AND d.delivered_at <= :dateTo '
                . ($userId ? 'AND d.created_by_id IN (' . implode(',', $userId) . ')' : '') . ' '
                . (!empty($servicesSql) ? 'AND di.id NOT IN (' . $servicesSql . ')' : '') . ' '
                . ($branchId ? 'AND cbb.id = :branchId' : '') . '
                GROUP BY c.gender,di.category
                ORDER BY di.sort
            ) src
        GROUP BY src.category
        ORDER BY src.sort

        ',$rsm);


//        $query = $this->em->createQuery(
//            '
//                SELECT
//                  di.category,
//                  COUNT(d.id) all_count,
//                  COUNT(DISTINCT d.client) client_count
//                FROM AppBundle\Entity\Delivery d
//                  JOIN d.deliveryItem di
//                  JOIN d.createdBy cb
//                  JOIN cb.branch cbb
//                WHERE d.deliveredAt >= :dateFrom AND d.deliveredAt <= :dateTo ' . ($userId ? 'AND d.createdBy IN (' . implode(',', $userId) . ')' : '') . '  ' . ($branchId ? 'AND cb.branch = :branchId' : '') . ' ' . (!empty($serviceSql) ? 'AND di.id NOT IN (' . $serviceSql . ')' : '') . '
//                  GROUP BY di.category
//                  ORDER BY di.sort'
//        );
        $parameters = [
            'dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];

        if ($branchId) {
            $parameters['branchId'] = $branchId;
        }
        $query->setParameters($parameters);
        //$res = $query->getResult();
        $res = $query->getScalarResult();

        foreach (array_keys($res) as $i) {
            $res[$i]['category'] = DeliveryItem::getCategoryNameById($res[$i]['category']);
        }

        return $res;
    }

    private function shelterReport($dateFrom = null, $dateTo = null, $userId = null)
    {
        $fields = [
            'Название',
            'Значение',
            '3 месяца',
            '6 месяцев',
            '1 год',
            '2 года'
        ];

        $this->doc->getActiveSheet()->fromArray([$fields], null, 'A1');


        // Drop temporary table;
        $this->em->getConnection()->exec('DROP TABLE IF EXISTS _cfrv2');

        // Create temporary table
        $this->em->getConnection()->exec('CREATE TEMPORARY TABLE _cfrv2(
            `client_form_response_id` INT(11) NOT NULL,
            `client_form_field_id` INT(11) NOT NULL,
            `client_id` INT(11) NOT NULL,
            `value` LONGTEXT CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `created_at` DATETIME DEFAULT NULL
        )');
        // $stmt->execute();

        // Create Index
        $this->em->getConnection()->exec('CREATE INDEX client_form_field_id ON _cfrv2(client_form_field_id)');
        // $stmt->execute();

        // Delimiter
        $this->em->getConnection()->exec("CREATE OR REPLACE PROCEDURE split_val(
            IN client_form_response_id INT,
            IN client_form_field_id INT,
            IN client_id INT,
            IN value LONGTEXT CHARSET utf8,
            IN created_at DATETIME
        )
        BEGIN
            SET @insert_sql = CONCAT('INSERT INTO _cfrv2(',
                                'client_form_response_id, client_form_field_id,',
                                'client_id, created_at, `value`',
                              ') VALUES (');
            SET @const_vals = CONCAT(
                QUOTE(client_form_response_id), ',',
                QUOTE(client_form_field_id), ',',
                QUOTE(client_id), ',',
                QUOTE(created_at), ','
            );
            SET @values_delim = CONCAT('\'),(\'', @const_vals, '\'');
			SET value = REPLACE(value,'\','');
			SET value = REPLACE(value, ''', '');
		    SET value = REPLACE(value, '', @values_delim);
		    SET @insert_rows = CONCAT(@insert_sql, @const_vals, '\'', value, '\')');
            EXECUTE IMMEDIATE @insert_rows;
        END");


        $this->em->getConnection()->exec("CREATE OR REPLACE PROCEDURE process_rows()
        BEGIN
            DECLARE done INT DEFAULT FALSE;
            DECLARE resp, field, client INT;
            DECLARE val LONGTEXT CHARACTER SET utf8;
            DECLARE creat DATETIME;
            DECLARE cur CURSOR FOR SELECT
                    client_form_response_id, client_form_field_id,
                    client_id, `value`, created_at
                    FROM client_form_response_value;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

            OPEN cur;

            read_loop: LOOP
                FETCH cur INTO resp, field, client, val, creat;
                IF done THEN
                    LEAVE read_loop;
                END IF;
                CALL split_val(resp, field, client, val, creat);
            END LOOP;

            CLOSE cur;
        END");

        $this->em->getConnection()->executeQuery("CALL process_rows()");

        $stmt = $this->em->getConnection()->prepare("SELECT cff.name,
        cfrv2.value,
        sum(if(q_type.value = '3 месяца', 1, 0))  as '3 месяца',
        sum(if(q_type.value = '6 месяцев', 1, 0)) as '6 месяцев',
        sum(if(q_type.value = '1 год', 1, 0)) as '1 год',
        sum(if(q_type.value = '2 года', 1, 0)) as '2 года'
            from `client_form_field` AS cff
          join `_cfrv2` AS cfrv2 on cfrv2.`client_form_field_id` = cff.id
          join (select r.client_id, r.`client_form_response_id`, r.`value` from `client_form_response_value` r where `client_form_field_id` = 1) as q_type on q_type.`client_id` = cfrv2.`client_id` and q_type.`client_form_response_id` = cfrv2.`client_form_response_id` where cff.`form_id` = 1 -- анкета проживавшего
        and cff.`type` = 2    -- если хотим убрать поля причина обращения (другое) и дата заполнения
        and cfrv2.`created_at` between :dateFrom and :dateTo
        group by cff.`id`, cff.`name`, cff.`sort`, cfrv2.`value`
        order by cff.`sort`");

        $params = [
            ':dateFrom' => $dateFrom ? $dateFrom : '1960-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d')
        ];

        $stmt->execute($params);

        return  $stmt->fetchAll();
    }
}
