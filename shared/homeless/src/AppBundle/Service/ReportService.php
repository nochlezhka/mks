<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ReportService
{
    const ONE_OFF_SERVICES = 'one_off_services';
    const ONE_OFF_SERVICES_USERS = 'one_off_services_users';
    const COMPLETED_ITEMS = 'completed_items';
    const COMPLETED_ITEMS_USERS = 'completed_items_users';
    const OUTGOING = 'outgoing';
    const RESULTS_OF_SUPPORT = 'results_of_support';
    const ACCOMPANYING = 'accompanying';

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

    public function getTypes(){
        return [
            static::ONE_OFF_SERVICES => 'Отчет о предоставленных разовых услугах',
            static::ONE_OFF_SERVICES_USERS => 'Отчет о предоставленных разовых услугах по сотрудникам',
            static::COMPLETED_ITEMS => 'Отчет о выполненных пунктах сервисного плана',
            static::COMPLETED_ITEMS_USERS => 'Отчет о выполненных пунктах сервисного плана по сотрудникам',
            static::OUTGOING => 'Отчет о выбывших из приюта',
            static::RESULTS_OF_SUPPORT => 'Отчет по результатам сопровождения ',
            static::ACCOMPANYING => 'Отчет по сопровождению',
        ];
    }

    /**
     * @param $type
     * @param null $dateFrom
     * @param null $dateTo
     * @param null $userId
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function generate($type, $dateFrom = null, $dateTo = null, $userId = null)
    {
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
                $result = $this->oneOffServices($dateFrom, $dateTo, $userId);
                break;

            case static::ONE_OFF_SERVICES_USERS:
                $result = $this->oneOffServicesUsers($dateFrom, $dateTo, $userId);
                break;

            case static::COMPLETED_ITEMS:
                $result = $this->completedItems($dateFrom, $dateTo, $userId);
                break;

            case static::COMPLETED_ITEMS_USERS:
                $result = $this->completedItemsUsers($dateFrom, $dateTo, $userId);
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
        }

        $this->doc->getActiveSheet()->fromArray($result, null, 'A2');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$type.'.xls"');
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
    private function oneOffServices($dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ]], null, 'A1');
        $query = $this->em->createQuery('SELECT st.name, COUNT(DISTINCT s.id) all_count, COUNT(DISTINCT s.client) client_count
            FROM AppBundle\Entity\Service s
            JOIN s.type st
            WHERE s.createdAt >= :dateFrom AND s.createdAt <= :dateTo ' . ($userId ? 'AND s.createdBy = :userId' : '') . '
            GROUP BY s.type
            ORDER BY st.sort');
        $parameters = [
            'dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters['userId'] = $userId;
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
    private function oneOffServicesUsers($dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ФИО сотрудникa',
            'название услуги',
            'сколько раз она была предоставлена'
        ]], null, 'A1');
        $stmt = $this->em->getConnection()->prepare('SELECT concat(u.lastname, \' \', u.firstname, \' \', u.middlename), st.name, COUNT(DISTINCT s.id) count
            FROM service s
            JOIN service_type st ON s.type_id = st.id
            LEFT JOIN fos_user_user u ON s.created_by_id = u.id
            WHERE s.created_at >= :dateFrom AND s.created_at <= :dateTo ' . ($userId ? 'AND s.created_by_id = :userId' : '') . '
            GROUP BY u.id, s.type_id
            ORDER BY st.sort');
        $parameters = [
            'dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            'dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters['userId'] = $userId;
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
    private function completedItems($dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'название услуги',
            'сколько раз она была предоставлена',
            'скольким людям она была предоставлена'
        ]], null, 'A1');
        $stmt = $this->em->getConnection()->prepare('SELECT cit.name, COUNT(DISTINCT i.id) all_count, COUNT(DISTINCT c.client_id) client_count
            FROM contract_item i
            JOIN contract c ON i.contract_id = c.id
            JOIN contract_item_type cit ON i.type_id = cit.id
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND ((i.created_by_id IS NOT NULL AND i.created_by_id = :userId) OR (i.created_by_id IS NULL AND c.created_by_id = :userId))' : '') . '
            GROUP BY i.type_id
            ORDER BY cit.sort');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
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
    private function completedItemsUsers($dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ФИО сотрудника',
            'название пункта',
            'сколько раз он был выполнен'
        ]], null, 'A1');
        $stmt = $this->em->getConnection()->prepare('SELECT concat(u.lastname, \' \', u.firstname, \' \', u.middlename) full_name, cit.name, COUNT(*) count
            FROM contract_item i
              JOIN contract c ON i.contract_id = c.id
              JOIN contract_item_type cit ON i.type_id = cit.id
              LEFT JOIN fos_user_user u ON (i.created_by_id IS NOT NULL AND i.created_by_id = u.id) OR (i.created_by_id IS NULL AND c.created_by_id = u.id)
            WHERE i.date >= :dateFrom AND i.date <= :dateTo ' . ($userId ? 'AND u.id = :userId' : '') . '
            GROUP BY i.type_id, u.id
            ORDER BY i.id, cit.sort');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
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
    private function outgoing($dateFrom = null, $dateTo = null, $userId = null)
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
        ]], null, 'A1');
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
            GROUP BY con.id
            ORDER BY h.date_to DESC');
        $parameters = [
            ':dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
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
    private function resultsOfSupport($dateFrom = null, $dateTo = null, $userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'выполненные пункты сервисного плана с комментариями',
            'невыполненные пункты сервисного плана с комментариями',
            'статус сервисного плана',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ]], null, 'A1');
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
            ':dateFrom' => $dateFrom ? $dateFrom : '2000-01-01',
            ':dateTo' => $dateTo ? $dateTo : date('Y-m-d'),
        ];
        if ($userId) {
            $parameters[':userId'] = $userId;
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
    private function accompanying($userId = null)
    {
        $this->doc->getActiveSheet()->fromArray([[
            'ID',
            'ФИО',
            'пункты сервисного плана с комментариями',
            'комментарии к сервисному плану в целом',
            'ФИО соцработника, открывшего сервисный план',
        ]], null, 'A1');
        $stmt = $this->em->getConnection()->prepare('SELECT c.id, concat(c.lastname, \' \', c.firstname, \' \', c.middlename), GROUP_CONCAT(CONCAT(cit1.name, \'(\' , ci1.comment, \')\')), cs.name, con.comment, concat(u.lastname, \' \', u.firstname, \' \', u.middlename)
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
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }
}
