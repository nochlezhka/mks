<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Service;

use App\Entity\ContractStatus;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

final class ReportService
{
    public const string ONE_OFF_SERVICES = 'one_off_services';
    public const string COMPLETED_ITEMS = 'completed_items';
    public const string OUTGOING = 'outgoing';
    public const string RESULTS_OF_SUPPORT = 'results_of_support';
    public const string ACCOMPANYING = 'accompanying';
    public const string AGGREGATED = 'aggregated';
    public const string AGGREGATED2 = 'aggregated2';

    private Spreadsheet $doc;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->doc = new Spreadsheet();
    }

    public function getTypes(): array
    {
        return [
            self::ONE_OFF_SERVICES => 'Отчет о предоставленных разовых услугах',
            self::COMPLETED_ITEMS => 'Отчет о выполненных пунктах сервисного плана',
            self::OUTGOING => 'Отчет о выбывших из приюта',
            self::RESULTS_OF_SUPPORT => 'Отчет по результатам сопровождения ',
            self::ACCOMPANYING => 'Отчет по сопровождению',
            self::AGGREGATED => 'Агрегированный отчет по соц-дем показателям',
            self::AGGREGATED2 => 'Отчет по количеству клиентов',
        ];
    }

    /**
     * @throws PhpSpreadsheetException
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function generate(
        mixed $type,
        mixed $dateFrom,
        mixed $dateTo,
        mixed $userId,
        mixed $clientDateFrom,
        mixed $clientDateTo,
        mixed $serviceDateFrom,
        mixed $serviceDateTo,
        mixed $homelessReason,
        mixed $disease,
        mixed $breadwinner,
    ): void {
        if ($dateFrom) {
            $time = strtotime($dateFrom);
            if ($time !== false) {
                $date = new \DateTimeImmutable();
                $date = $date->setTimestamp($time);
                $dateFrom = $date->format('Y-m-d');
            }
        }

        if ($dateTo) {
            $time = strtotime($dateTo);
            if ($time !== false) {
                $date = new \DateTimeImmutable();
                $date = $date->setTimestamp($time);
                $dateTo = $date->format('Y-m-d');
            }
        }

        $result = match ($type) {
            self::ONE_OFF_SERVICES => $this->oneOffServices($dateFrom, $dateTo, $userId),
            self::COMPLETED_ITEMS => $this->completedItems($dateFrom, $dateTo, $userId),
            self::OUTGOING => $this->outgoing($dateFrom, $dateTo, $userId),
            self::RESULTS_OF_SUPPORT => $this->resultsOfSupport($dateFrom, $dateTo, $userId),
            self::ACCOMPANYING => $this->accompanying($userId),
            self::AGGREGATED => $this->aggregated($clientDateFrom, $clientDateTo, $serviceDateFrom, $serviceDateTo),
            self::AGGREGATED2 => $this->aggregated2($clientDateFrom, $clientDateTo, $serviceDateFrom, $serviceDateTo, $homelessReason, $disease, $breadwinner),
            default => throw new \RuntimeException('Unexpected report type "'.$type.'"'),
        };

        $this->doc->setActiveSheetIndex(0);
        $this->doc->getActiveSheet()->fromArray($result, null, 'A2');
        $writer = new Xls($this->doc);
        $writer->save('php://output');
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function getClients(
        mixed $serviceDateFrom,
        mixed $serviceDateTo,
        mixed $clientDateFrom,
        mixed $clientDateTo,
    ): array {
        if ($serviceDateFrom || $serviceDateTo) {
            $stmt = $this->entityManager->getConnection()->prepare('
            SELECT c.id
            FROM client c
              JOIN service s
                ON s.client_id = c.id
            WHERE s.created_at >= :serviceDateFrom
              AND s.created_at <= :serviceDateTo
              AND c.created_at >= :clientDateFrom
              AND c.created_at <= :clientDateTo
            ');
            $stmt->bindValue('serviceDateFrom', $serviceDateFrom ? date('Y-m-d', strtotime($serviceDateFrom)) : '1960-01-01');
            $stmt->bindValue('serviceDateTo', $serviceDateTo ? date('Y-m-d', strtotime($serviceDateTo)) : date('Y-m-d'));
        } else {
            $stmt = $this->entityManager->getConnection()->prepare('
            SELECT c.id
            FROM client c
            WHERE c.created_at >= :clientDateFrom
              AND c.created_at <= :clientDateTo
            ');
        }
        $stmt->bindValue('clientDateFrom', $clientDateFrom ? date('Y-m-d', strtotime($clientDateFrom)) : '1960-01-01');
        $stmt->bindValue('clientDateTo', $clientDateTo ? date('Y-m-d', strtotime($clientDateTo)) : date('Y-m-d'));

        return $stmt->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function oneOffServices(
        mixed $dateFrom,
        mixed $dateTo,
        mixed $userId,
    ): array {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена',
        ]]);
        $stmt = $this->entityManager->getConnection()->prepare('
            SELECT
                st.name,
                COUNT(DISTINCT s.id) all_count,
                COUNT(DISTINCT s.client_id) client_count
            FROM service s
              JOIN service_type st
                ON s.type_id = st.id
            WHERE s.created_at >= :dateFrom
              AND s.created_at <= :dateTo '.($userId ? 'AND s.created_by_id = :userId' : '').'
            GROUP BY st.id
            ORDER BY st.sort
        ');
        $stmt->bindValue('dateFrom', $dateFrom ?: '1960-01-01');
        $stmt->bindValue('dateTo', $dateTo ?: date('Y-m-d'));
        if ($userId) {
            $stmt->bindValue('userId', $userId);
        }

        return $stmt->executeQuery()->fetchAllNumeric();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function completedItems(
        mixed $dateFrom,
        mixed $dateTo,
        mixed $userId,
    ): array {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена',
        ]]);
        $excludeStatuses = [
            ContractStatus::IN_PROCESS,
            ContractStatus::REJECTED_CLIENT_REFUSAL,
            ContractStatus::REJECTED_OTHER,
            ContractStatus::REJECTED_CLIENT_NON_APPEARANCE,
        ];

        if ($userId) {
            return $this->entityManager->getConnection()->executeQuery('
                SELECT cit.name,
                       COUNT(DISTINCT i.id) all_count,
                       COUNT(DISTINCT c.client_id) client_count
                FROM contract_item i
                  JOIN contract c
                    ON i.contract_id = c.id
                  JOIN contract_item_type cit
                    ON i.type_id = cit.id
                WHERE c.status_id NOT IN (:excludeStatuses)
                  AND i.date >= :dateFrom
                  AND i.date <= :dateTo
                  AND ((i.created_by_id IS NOT NULL AND i.created_by_id = :userId) OR (i.created_by_id IS NULL AND c.created_by_id = :userId))
                GROUP BY i.type_id
                ORDER BY cit.sort
            ', [
                'excludeStatuses' => $excludeStatuses,
                'dateFrom' => $dateFrom ?: '1960-01-01',
                'dateTo' => $dateTo ?: date('Y-m-d'),
                'userId' => $userId,
            ], [
                'excludeStatuses' => ArrayParameterType::INTEGER,
            ])->fetchAllNumeric();
        }

        return $this->entityManager->getConnection()->executeQuery('
            SELECT cit.name,
                   COUNT(DISTINCT i.id) all_count,
                   COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
              JOIN contract c
                ON i.contract_id = c.id
              JOIN contract_item_type cit
                ON i.type_id = cit.id
            WHERE c.status_id NOT IN (:excludeStatuses)
              AND i.date >= :dateFrom
              AND i.date <= :dateTo
            GROUP BY i.type_id
            ORDER BY cit.sort
        ', [
            'excludeStatuses' => $excludeStatuses,
            'dateFrom' => $dateFrom ?: '1960-01-01',
            'dateTo' => $dateTo ?: date('Y-m-d'),
        ], [
            'excludeStatuses' => ArrayParameterType::INTEGER,
        ])->fetchAllNumeric();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function outgoing(
        mixed $dateFrom,
        mixed $dateTo,
        mixed $userId,
    ): array {
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
        $stmt = $this->entityManager->getConnection()->prepare('
            SELECT c.id,
                   concat(c.lastname, \' \', c.firstname, \' \', c.middlename),
                   h.date_from,
                   h.date_to,
                   GROUP_CONCAT(CONCAT(cit1.name, COALESCE(CONCAT(\'(\', ci1.comment + \')\'), \'\'))),
                   GROUP_CONCAT(CONCAT(cit2.name, COALESCE(CONCAT(\'(\', ci2.comment + \')\'), \'\'))),
                   cs.name,
                   con.comment,
                   concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
              JOIN shelter_history h
                ON con.id = h.contract_id
              JOIN fos_user_user u
                ON con.created_by_id = u.id
              JOIN client c
                ON con.client_id = c.id
              LEFT JOIN contract_item ci1
                ON con.id = ci1.contract_id AND ci1.date IS NOT NULL
              LEFT JOIN contract_item_type cit1
                ON ci1.type_id = cit1.id
              LEFT JOIN contract_item ci2
                ON con.id = ci2.contract_id AND ci2.date IS NULL
              LEFT JOIN contract_item_type cit2
                ON ci2.type_id = cit2.id
              JOIN contract_status cs
                ON con.status_id = cs.id
            WHERE h.date_to >= :dateFrom
              AND h.date_to <= :dateTo
              '.($userId ? 'AND u.id = :userId' : '').'
            GROUP BY con.id, h.id
            ORDER BY h.date_to DESC
        ');
        $stmt->bindValue('dateFrom', $dateFrom ?: '1960-01-01');
        $stmt->bindValue('dateTo', $dateTo ?: date('Y-m-d'));
        if ($userId) {
            $stmt->bindValue('userId', $userId);
        }

        return $stmt->executeQuery()->fetchAllNumeric();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function resultsOfSupport(
        mixed $dateFrom,
        mixed $dateTo,
        mixed $userId,
    ): array {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ]]);
        $stmt = $this->entityManager->getConnection()->prepare('
            SELECT c.id,
                   concat(c.lastname, \' \', c.firstname, \' \', c.middlename),
                   GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')),
                   GROUP_CONCAT(CONCAT(cit2.name, \'(\' , ci2.comment, \')\')),
                   cs.name,
                   con.comment,
                   concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
              JOIN fos_user_user u
                ON con.created_by_id = u.id
              JOIN client c
                ON con.client_id = c.id
              LEFT JOIN contract_item ci1
                ON con.id = ci1.contract_id AND ci1.date IS NOT NULL
              LEFT JOIN contract_item_type cit1
                ON ci1.type_id = cit1.id
              LEFT JOIN contract_item ci2
                ON con.id = ci2.contract_id AND ci2.date IS NULL
              LEFT JOIN contract_item_type cit2
                ON ci2.type_id = cit2.id
              JOIN contract_status cs
                ON con.status_id = cs.id
            WHERE con.date_to >= :dateFrom
              AND con.date_to <= :dateTo
              '.($userId ? 'AND u.id = :userId' : '').'
              AND con.status_id = 2
            GROUP BY con.id
            ORDER BY con.date_to DESC
        ');
        $stmt->bindValue('dateFrom', $dateFrom ?: '1960-01-01');
        $stmt->bindValue('dateTo', $dateTo ?: date('Y-m-d'));
        if ($userId) {
            $stmt->bindValue('userId', $userId);
        }

        return $stmt->executeQuery()->fetchAllNumeric();
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function accompanying(mixed $userId): array
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
        $stmt = $this->entityManager->getConnection()->prepare('
            SELECT c.id,
                   concat(c.lastname, \' \', c.firstname, \' \', c.middlename),
                   GROUP_CONCAT(CONCAT(cit1.name, COALESCE(CONCAT(\'(\', ci1.comment + \')\'), \'\'))),
                   cs.name,
                   con.comment,
                   TO_DAYS(con.date_to) - TO_DAYS(con.date_from),
                   concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
            FROM contract con
              JOIN fos_user_user u
                ON con.created_by_id = u.id
              JOIN client c
                ON con.client_id = c.id
              LEFT JOIN contract_item ci1
                ON con.id = ci1.contract_id
              LEFT JOIN contract_item_type cit1
                ON ci1.type_id = cit1.id
              JOIN contract_status cs
                ON con.status_id = cs.id
            WHERE '.($userId ? ' u.id = :userId AND ' : '').'
                status_id = 1
            GROUP BY con.id
            ORDER BY con.date_to DESC
        ');
        if ($userId) {
            $stmt->bindValue('userId', $userId);
        }

        return $stmt->executeQuery()->fetchAllNumeric();
    }

    /**
     * @throws DBALDriverException
     * @throws DBALException
     */
    private function aggregated(
        mixed $clientDateFrom,
        mixed $clientDateTo,
        mixed $serviceDateFrom,
        mixed $serviceDateTo,
    ): array {
        $this->doc->getActiveSheet()->fromArray([[
            'Вопрос',
            'Ответ',
            'Количество',
        ]]);

        $clients = $this->getClients($serviceDateFrom, $serviceDateTo, $clientDateFrom, $clientDateTo);
        if (empty($clients)) {
            return [];
        }

        $clientsIds = [];
        foreach ($clients as $item) {
            $clientsIds[] = $item['id'];
        }
        $clientsIds = implode(',', array_unique($clientsIds));

        $clientCount = $this->entityManager->getConnection()->prepare('
            SELECT COUNT(*)
            FROM client c
            WHERE c.id IN ('.$clientsIds.')
        ')->executeQuery()->fetchOne();
        $menCount = $this->entityManager->getConnection()->prepare('
            SELECT COUNT(*)
            FROM client c
            WHERE c.id IN ('.$clientsIds.')
              AND c.gender = 1
        ')->executeQuery()->fetchOne();
        $womenCount = $this->entityManager->getConnection()->prepare('
            SELECT COUNT(*)
            FROM client c
            WHERE c.id IN ('.$clientsIds.')
              AND c.gender = 2
        ')->executeQuery()->fetchOne();
        $awgAge = $this->entityManager->getConnection()->prepare('
            SELECT CAST(AVG(TIMESTAMPDIFF(YEAR,c.birth_date,curdate())) AS UNSIGNED)
            FROM client c
            WHERE c.id IN ('.$clientsIds.')
        ')->executeQuery()->fetchOne();
        $awgHomelessPeriod = $this->entityManager->getConnection()->prepare('
            SELECT CAST(AVG(TIMESTAMPDIFF(YEAR,cfv.datetime,curdate())) AS UNSIGNED)
            FROM client c
                JOIN client_field cf
                    ON cf.code = \'homelessFrom\'
                JOIN client_field_value cfv
                    ON c.id = cfv.client_id AND cfv.field_id = cf.id
            WHERE c.id IN ('.$clientsIds.')',
        )->executeQuery()->fetchOne();
        $haveWork = $this->entityManager->getConnection()->prepare('
            SELECT COUNT(*)
            FROM client c
              JOIN client_field_value cfv
                ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
              JOIN client_field cf
                ON cfv.field_id = cf.id
              JOIN client_field_value_client_field_option cfvcfo
                ON cfv.id = cfvcfo.client_field_value_id
              JOIN client_field_option cfo
                ON cfvcfo.client_field_option_id = cfo.id
            WHERE c.id IN ('.$clientsIds.')
              AND cf.code = \'profession\'
        ')->executeQuery()->fetchOne();

        $fieldStat = $this->entityManager->getConnection()->prepare('
            SELECT cf.name,
                   cfo.name,
                   COUNT(*)
            FROM client c
              JOIN client_field_value cfv
                ON c.id = cfv.client_id AND cfv.option_id IS NULL AND cfv.datetime IS NULL AND cfv.text IS NULL
              JOIN client_field cf
                ON cfv.field_id = cf.id
              JOIN client_field_value_client_field_option cfvcfo
                ON cfv.id = cfvcfo.client_field_value_id
              JOIN client_field_option cfo
                ON cfvcfo.client_field_option_id = cfo.id
            WHERE c.id IN ('.$clientsIds.')
              AND cf.code != \'profession\'
            GROUP BY cf.name, cfo.name
        ')->executeQuery()->fetchAllNumeric();

        $fieldStat2 = $this->entityManager->getConnection()->prepare('
            SELECT cf.name,
                   cfo.name,
                   COUNT(*)
            FROM client c
              JOIN client_field_value cfv
                ON c.id = cfv.client_id AND cfv.option_id IS NOT NULL
              JOIN client_field cf
                ON cfv.field_id = cf.id
              JOIN client_field_option cfo
                ON cfv.option_id = cfo.id
            WHERE c.id IN ('.$clientsIds.')
              AND cf.code != \'profession\'
            GROUP BY cf.name, cfo.name
        ')->executeQuery()->fetchAllNumeric();

        $sheetData = [
            ['Количество', 'Общее', $clientCount],
            ['Количество', 'Мужчин', $menCount],
            ['Количество', 'Женщин', $womenCount],
            ['Средний', 'Возраст', $awgAge],
            ['Средний', 'Стаж бездомности', $awgHomelessPeriod],
            ['Профессия', 'Есть', $haveWork],
            ['Профессия', 'Нет', (int) $clientCount - (int) $haveWork],
        ];
        array_push($sheetData, ...$fieldStat);
        array_push($sheetData, ...$fieldStat2);

        return $sheetData;
    }

    /**
     * @throws DBALException
     * @throws DBALDriverException
     */
    private function aggregated2(
        mixed $clientDateFrom,
        mixed $clientDateTo,
        mixed $serviceDateFrom,
        mixed $serviceDateTo,
        mixed $homelessReason,
        mixed $disease,
        mixed $breadwinner,
    ): array {
        $this->doc->getActiveSheet()->fromArray([[
            'Количество',
        ]]);

        $clients = $this->getClients($serviceDateFrom, $serviceDateTo, $clientDateFrom, $clientDateTo);
        if (empty($clients)) {
            return [];
        }

        $clientsCounts = [];
        foreach ($clients as $item) {
            $clientsCounts[$item['id']] = 0;
        }

        if ($homelessReason || $disease || $breadwinner) {
            if ($disease) {
                $stmt = $this->entityManager->getConnection()->prepare('
                    SELECT c.id
                    FROM client c
                      LEFT JOIN client_field_value cfv
                        ON c.id = cfv.client_id
                      LEFT JOIN client_field_value_client_field_option cfvcfo
                        ON cfv.id = cfvcfo.client_field_value_id
                      LEFT JOIN client_field cf
                        ON cfv.field_id = cf.id
                    WHERE cf.code = \'disease\'
                      AND cfvcfo.client_field_option_id IN ('.implode(',', $disease).')
                ');
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $client) {
                    if (!isset($clientsCounts[$client['id']])) {
                        continue;
                    }
                    ++$clientsCounts[$client['id']];
                }
            }
            if ($homelessReason) {
                $stmt = $this->entityManager->getConnection()->prepare('
                    SELECT c.id
                    FROM client c
                      LEFT JOIN client_field_value cfv
                        ON c.id = cfv.client_id
                      LEFT JOIN client_field_value_client_field_option cfvcfo
                        ON cfv.id = cfvcfo.client_field_value_id
                      LEFT JOIN client_field cf
                        ON cfv.field_id = cf.id
                    WHERE cf.code = \'homelessReason\'
                      AND cfvcfo.client_field_option_id IN ('.implode(',', $homelessReason).')
                ');
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $client) {
                    if (!isset($clientsCounts[$client['id']])) {
                        continue;
                    }
                    ++$clientsCounts[$client['id']];
                }
            }
            if ($breadwinner) {
                $stmt = $this->entityManager->getConnection()->prepare('
                    SELECT c.id
                    FROM client c
                      LEFT JOIN client_field_value cfv
                        ON c.id = cfv.client_id
                      LEFT JOIN client_field_value_client_field_option cfvcfo
                        ON cfv.id = cfvcfo.client_field_value_id
                      LEFT JOIN client_field cf
                        ON cfv.field_id = cf.id
                    WHERE cf.code = \'breadwinner\'
                      AND cfvcfo.client_field_option_id IN ('.implode(',', $breadwinner).')
                ');
                foreach ($stmt->executeQuery()->fetchAllAssociative() as $client) {
                    if (!isset($clientsCounts[$client['id']])) {
                        continue;
                    }
                    ++$clientsCounts[$client['id']];
                }
            }
            $max = max($clientsCounts);
            foreach ($clientsCounts as $clientsId => $value) {
                if ($value !== $max) {
                    unset($clientsCounts[$clientsId]);
                }
            }
        }

        return $this->entityManager->getConnection()->executeQuery('
            SELECT COUNT(*)
            FROM client c
            WHERE c.id IN (:clientsIds)
        ', [
            'clientsIds' => array_keys($clientsCounts),
        ], [
            'clientsIds' => ArrayParameterType::INTEGER,
        ])->fetchAllNumeric();
    }
}
