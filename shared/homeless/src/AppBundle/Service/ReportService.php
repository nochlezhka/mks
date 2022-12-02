<?php

namespace AppBundle\Service;

use DateTime;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

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

    private EntityManager $em;
    private Spreadsheet $doc;

    /**
     * CertificateRecreator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->doc = new Spreadsheet();
    }

    public function getTypes(): array
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
     * @throws PhpSpreadsheetException
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function generate($type, $dateFrom = null, $dateTo = null, $userId = null, $createClientdateFrom = null, $createClientFromTo = null, $createServicedateFrom = null, $createServiceFromTo = null, $homelessReason = null, $disease = null, $breadwinner = null)
    {
        if ($dateFrom) {
            $date = new DateTime();
            $date->setTimestamp(strtotime($dateFrom));
            $dateFrom = $date->format('Y-m-d');
        }

        if ($dateTo) {
            $date = new DateTime();
            $date->setTimestamp(strtotime($dateTo));
            $dateTo = $date->format('Y-m-d');
        }

        $result = [];
        $this->doc->setActiveSheetIndex(0);
        switch ($type) {
            case static::ONE_OFF_SERVICES:
                $result = $this->oneOffServices($dateFrom, $dateTo, $userId);
                break;

            case static::COMPLETED_ITEMS:
                $result = $this->completedItems($dateFrom, $dateTo, $userId);
                break;

            case static::OUTGOING:
                $result = $this->outgoing($dateFrom, $dateTo, $userId);
                break;

            case static::RESULTS_OF_SUPPORT:
                $result = $this->resultsOfSupport($dateFrom, $dateTo, $userId);
                break;

            case static::ACCOMPANYING:
                $result = $this->accompanying($userId);
                break;
            case static::AVERAGE_COMPLETED_ITEMS:
                $result = $this->averageCompletedItems($dateFrom, $dateTo, $userId);
                break;

            case static::AGGREGATED:
                $result = $this->aggregated($createClientdateFrom, $createClientFromTo, $createServicedateFrom, $createServiceFromTo);
                break;

            case static::AGGREGATED2:
                $result = $this->aggregated2($createClientdateFrom, $createClientFromTo, $createServicedateFrom, $createServiceFromTo, $homelessReason, $disease, $breadwinner);
                break;
        }

        $this->doc->getActiveSheet()->fromArray($result, null, 'A2');
        $writer = new Xls($this->doc);
        $writer->save('php://output');
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function oneOffServices($dateFrom = null, $dateTo = null, $userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена',
            'сумма'
        ]]);
        $statement = $this->em->getConnection()->prepare('
            SELECT
                st.name, 
                COUNT(DISTINCT s.id) all_count, 
                COUNT(DISTINCT s.client_id) client_count, 
                SUM(s.amount) as sum_amount
            FROM service s
            JOIN service_type st on s.type_id = st.id
            WHERE s.created_at >= :dateFrom AND s.created_at <= :dateTo ' . ($userId ? 'AND s.created_by_id = :userId' : '') . '
            GROUP BY st.id
            ORDER BY st.sort');
        $parameters = [
            'dateFrom' => $dateFrom ?: '1960-01-01',
            'dateTo' => $dateTo ?: date('Y-m-d'),
        ];
        if ($userId) {
            $parameters['userId'] = $userId;
        }
        return $statement->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function completedItems($dateFrom = null, $dateTo = null, $userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ]]);
        $stmt = $this->em->getConnection()->prepare('SELECT cit.name, COUNT(DISTINCT i.id) all_count, COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id = :userId) OR (i.created_by_id IS NULL AND c.created_by_id = :userId))' : '') . '
            GROUP BY i.type_id
            ORDER BY cit.sort');
        $parameters = [
            ':dateFrom' => $dateFrom ?: '1960-01-01',
            ':dateTo' => $dateTo ?: date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
        }
        return $stmt->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function outgoing($dateFrom = null, $dateTo = null, $userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'Дата заселения',
            'Дата выселения',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана на момент выселения',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ]]);
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
            WHERE h.date_to >= :dateFrom AND h.date_to <= :dateTo ' . ($userId ? 'AND u.id = :userId' : '') . '
            GROUP BY con.id, h.id
            ORDER BY h.date_to DESC');
        $parameters = [
            ':dateFrom' => $dateFrom ?: '1960-01-01',
            ':dateTo' => $dateTo ?: date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
        }
        return $stmt->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function resultsOfSupport($dateFrom = null, $dateTo = null, $userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ]]);
        $stmt = $this->em->getConnection()->prepare('SELECT c.id, concat(c.lastname, \' \', c.firstname, \' \', c.middlename), GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')), GROUP_CONCAT(CONCAT(cit2.name, \'(\' , ci2.comment, \')\')), cs.name, con.comment, concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN contract_item ci1 ON con.id = ci1.contract_id AND ci1.date IS NOT NULL
            LEFT JOIN contract_item_type cit1 ON ci1.type_id = cit1.id
            LEFT JOIN contract_item ci2 ON con.id = ci2.contract_id AND ci2.date IS NULL
            LEFT JOIN contract_item_type cit2 ON ci2.type_id = cit2.id
            JOIN contract_status cs ON con.status_id = cs.id
            WHERE con.date_to >= :dateFrom AND con.date_to <= :dateTo ' . ($userId ? 'AND u.id = :userId' : '') . ' 
                AND con.status_id = 2
            GROUP BY con.id
            ORDER BY con.date_to DESC');
        $parameters = [
            ':dateFrom' => $dateFrom ?: '1960-01-01',
            ':dateTo' => $dateTo ?: date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
        }
        return $stmt->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function accompanying($userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'пункты сервисного плана с комментариями',
            'статус',
            'комментарии к сервисному плану в целом',
            'длительность выполнения',
            'ФИО соцработника, открывшего сервисный план',
        ]]);
        $stmt = $this->em->getConnection()->prepare('SELECT 
              c.id, 
              concat(c.lastname, \' \', c.firstname, \' \', c.middlename), 
              GROUP_CONCAT(CONCAT(cit1.name, COALESCE(CONCAT(\'(\', ci1.comment + \')\'), \'\'))), 
              cs.name,  
              con.comment, 
              TO_DAYS(con.date_to) - TO_DAYS(con.date_from),
              concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
            JOIN fos_user_user u ON con.created_by_id = u.id
            JOIN client c ON con.client_id = c.id
            LEFT JOIN contract_item ci1 ON con.id = ci1.contract_id
            LEFT JOIN contract_item_type cit1 ON ci1.type_id = cit1.id
            JOIN contract_status cs ON con.status_id = cs.id
            WHERE ' . ($userId ? ' u.id = :userId AND ' : '') . '
                status_id = 1
            GROUP BY con.id
            ORDER BY con.date_to DESC');
        $parameters = [];
        if ($userId) {
            $parameters[':userId'] = $userId;
        }
        return $stmt->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @return array
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function averageCompletedItems($dateFrom = null, $dateTo = null, $userId = null): array
    {
        $this->doc->getActiveSheet()->fromArray([[
            'название пункта',
            'средняя длительность',
        ]]);
        $stmt = $this->em->getConnection()->prepare('SELECT 
                cit.name, 
                FLOOR(AVG (TO_DAYS(c.date_to) - TO_DAYS(c.date_from))) avg_days
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id = :userId) OR (i.created_by_id IS NULL AND c.created_by_id = :userId))' : '') . '
            GROUP BY cit.name
            ORDER BY cit.name');
        $parameters = [
            ':dateFrom' => $dateFrom ?: '2000-01-01',
            ':dateTo' => $dateTo ?: date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
        }
        return $stmt->executeQuery($parameters)->fetchAllNumeric();
    }

    /**
     * @param null $createClientdateFrom
     * @param null $createClientFromTo
     * @param null $createServicedateFrom
     * @param null $createServiceFromTo
     * @return array
     * @throws DBALDriverException
     * @throws DBALException
     */
    private function aggregated($createClientdateFrom = null, $createClientFromTo = null, $createServicedateFrom = null, $createServiceFromTo = null): array
    {

        $this->doc->getActiveSheet()->fromArray([[
            'Вопрос',
            'Ответ',
            'Количество',
        ]]);
        $result = $this->getClients($createServicedateFrom, $createServiceFromTo, $createClientdateFrom, $createClientFromTo);
        $clientsIds = [];
        foreach ($result as $item) {
            $clientsIds[] = $item['id'];
        }
        $clientsIds = array_unique($clientsIds);
        if (!$clientsIds) {
            return [];
        }

        $clientCount = $this->em->getConnection()->prepare(
            'SELECT COUNT(*) FROM client c WHERE c.id IN (' . implode(',', $clientsIds) . ')'
        )->executeQuery()->fetchOne();
        $menCount = $this->em->getConnection()->prepare(
            'SELECT COUNT(*) FROM client c 
                 WHERE c.id IN (' . implode(',', $clientsIds) . ') AND c.gender = 1'
        )->executeQuery()->fetchOne();
        $womenCount = $this->em->getConnection()->prepare(
            'SELECT COUNT(*) FROM client c 
                 WHERE c.id IN (' . implode(',', $clientsIds) . ') AND c.gender = 2'
        )->executeQuery()->fetchOne();
        $awgAge = $this->em->getConnection()->prepare(
            'SELECT CAST(AVG(TIMESTAMPDIFF(YEAR,c.birth_date,curdate())) AS UNSIGNED)
                 FROM client c WHERE c.id IN (' . implode(',', $clientsIds) . ')'
        )->executeQuery()->fetchOne();
        $awgHomelessPeriod = $this->em->getConnection()->prepare(
            'SELECT CAST(AVG(TIMESTAMPDIFF(YEAR,cfv.datetime,curdate())) AS UNSIGNED)
                FROM client c
                JOIN client_field cf ON cf.code = \'homelessFrom\'
                JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.field_id = cf.id
                WHERE c.id IN (' . implode(',', $clientsIds) . ')'
        )->executeQuery()->fetchOne();
        $haveWork = $this->em->getConnection()->prepare(
            'SELECT COUNT(*)
                FROM client c
                JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
                JOIN client_field cf ON cfv.field_id = cf.id
                JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
                JOIN client_field_option cfo on cfvcfo.client_field_option_id = cfo.id
                WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code = \'profession\''
        )->executeQuery()->fetchOne();

        $fieldStat = $this->em->getConnection()->prepare(
            'SELECT cf.name, cfo.name, COUNT(*)
                FROM client c
                JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
                JOIN client_field cf ON cfv.field_id = cf.id
                JOIN client_field_value_client_field_option cfvcfo on cfv.id = cfvcfo.client_field_value_id
                JOIN client_field_option cfo on cfvcfo.client_field_option_id = cfo.id
                WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code != \'profession\'
                GROUP BY cf.name, cfo.name'
        )->executeQuery()->fetchAllNumeric();

        $fieldStat2 = $this->em->getConnection()->prepare(
            'SELECT cf.name, cfo.name, COUNT(*)
                FROM client c
                JOIN client_field_value cfv ON c.id = cfv.client_id AND cfv.option_id IS NOT NULL
                JOIN client_field cf ON cfv.field_id = cf.id
                JOIN client_field_option cfo on cfv.option_id = cfo.id
                WHERE c.id IN (' . implode(',', $clientsIds) . ') and cf.code != \'profession\'
                GROUP BY cf.name, cfo.name'
        )->executeQuery()->fetchAllNumeric();

        $sheetData = [
            ['Количество', 'Общее', $clientCount],
            ['Количество', 'Мужчин', $menCount],
            ['Количество', 'Женщин', $womenCount],
            ['Средний', 'Возраст', $awgAge],
            ['Средний', 'Стаж бездомности', $awgHomelessPeriod],
            ['Профессия', 'Есть', $haveWork],
            ['Профессия', 'Нет', intval($clientCount) - intval($haveWork)]
        ];
        array_push($sheetData, ...$fieldStat);
        array_push($sheetData, ...$fieldStat2);
        return $sheetData;
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
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function aggregated2($createClientdateFrom = null, $createClientFromTo = null, $createServicedateFrom = null, $createServiceFromTo = null, $homelessReason = null, $disease = null, $breadwinner = null): array
    {

        $this->doc->getActiveSheet()->fromArray([[
            'Количество',
        ]]);
        $result = $this->getClients($createServicedateFrom, $createServiceFromTo, $createClientdateFrom, $createClientFromTo);
        $clientsIds = [];
        foreach ($result as $item) {
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
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $item) {
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
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $item) {
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
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $item) {
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
            $clientsIds = array_keys($clientsIds);
        }
        if ($clientsIds === []) {
            return [];
        }
        $stmt = $this->em->getConnection()->prepare('
  SELECT COUNT(*)
  FROM client c
  WHERE c.id IN (' . implode(',', $clientsIds) . ')');
        return $stmt->executeQuery()->fetchAllNumeric();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function getClients($createServicedateFrom, $createServiceFromTo, $createClientdateFrom, $createClientFromTo): array
    {
        if ($createServicedateFrom || $createServiceFromTo) {
            $stmt = $this->em->getConnection()->prepare('SELECT c.id
            FROM client c
            JOIN service s ON s.client_id = c.id
            WHERE s.created_at >= :createServicedateFrom AND s.created_at <= :createServiceFromTo AND
                  c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo '
            );
            $parameters = [
                ':createServicedateFrom' => $createServicedateFrom ? date('Y-m-d', strtotime($createServicedateFrom)) : '1960-01-01',
                ':createServiceFromTo' => $createServiceFromTo ? date('Y-m-d', strtotime($createServiceFromTo)) : date('Y-m-d'),
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        } else {
            $stmt = $this->em->getConnection()->prepare('SELECT c.id
            FROM client c
            WHERE c.created_at >= :createClientdateFrom AND c.created_at <= :createClientFromTo '
            );
            $parameters = [
                ':createClientdateFrom' => $createClientdateFrom ? date('Y-m-d', strtotime($createClientdateFrom)) : '1960-01-01',
                ':createClientFromTo' => $createClientFromTo ? date('Y-m-d', strtotime($createClientFromTo)) : date('Y-m-d'),
            ];
        }
        return $stmt->executeQuery($parameters)->fetchAllAssociative();
    }
}
