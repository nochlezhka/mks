<?php


namespace App\Command;


use App\Entity\ClientFormResponse;
use App\Service\MetaService;
use App\Service\ResidentQuestionnaireConverter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Logger\DbalLogger;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Общие функции для homeless:resident_qnr:copy и homeless:resident_qnr:check_and_switch
 * @package App\Command
 */
abstract class ResidentQnrCommon extends ContainerAwareCommand
{
    const DEFAULT_BATCH_SIZE = 500;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var ResidentQuestionnaireConverter
     */
    protected $residentQuestionnaireConverter;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetaService
     */
    protected $metaService;


    protected function initDependencies()
    {
        $this->residentQuestionnaireConverter = $this->getContainer()->get('app.resident_questionnaire_converter');
        $this->metaService = $this->getContainer()->get('app.meta_service');
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param OutputInterface $output
     * @param string $loggerName
     * @throws Exception
     */
    protected function initLogging(OutputInterface $output, $loggerName)
    {
        $this->logger = new Logger($loggerName);
        $this->logger->pushHandler(new StreamHandler(
            $this->getContainer()->getParameter('kernel.logs_dir') . '/resident_qnr.log'
        ));
        $this->logger->pushHandler(new ConsoleHandler($output));
        // если указан -vvv, показываем на экране и в логе SQL запросы
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(new DbalLogger($this->logger));
        }
    }

    /**
     * @return bool `false` если анкеты в новом формате уже включены
     */
    protected function checkEnabled()
    {
        if ($this->metaService->isClientFormsEnabled()) {
            $this->logger->error("client forms are already enabled");
            return false;
        }
        return true;
    }

    /**
     * @return bool `false` если структура формы "Анкета проживающего" не совпадает со структурой старой формы
     */
    protected function checkClientFormSchema()
    {
        $this->logger->info("checking resident questionnaire client form schema");
        if (!$this->residentQuestionnaireConverter->checkClientFormSchema($this->logger)) {
            $this->logger->error("validation failed!");
            return false;
        }
        $this->logger->info("ok");
        return true;
    }

    /**
     * Внутри транзакции выбирает пачку анкет, отсортированных по id и вызывает колбек.
     * Транзакция полезна, т.к. позволяет прочитать консистентные копии анкет в колбеке.
     *
     * Параметры колбека: `(ResidentQuestionnaire[] $qnrArray, int $idFrom, int|null $idUntil)`
     * `$idFrom` - начало диапазона айдишников анкет, не включительно.
     * `$idUntil` - последний айдишник анкеты или `null`, если все анкеты уже были обработаны.
     *
     * @param callable $cb
     * @param string $join сюда можно добавить дополнительные JOIN, если они будут нужны для фильтрации
     * @param string $where SQL условие на анкеты (у анкет алиас `qnr`)
     * @param int $batchSize размер пачки анкет
     * @throws Exception
     */
    protected function batchEnumQnr($cb, $join = "", $where = "1 = 1", $batchSize = self::DEFAULT_BATCH_SIZE)
    {
        $finished = false;
        $lastId = 0;
        // на всякий случай делаем flush, т.к. будем делать entityManager->clear() внутри цикла
        $this->entityManager->flush();
        // на всякий случай явно указываем, что хотим получать в транзакции фиксированные версии строк
        $this->entityManager->getConnection()->setTransactionIsolation(Connection::TRANSACTION_REPEATABLE_READ);
        while (!$finished) {
            $this->entityManager->transactional(
                function ($em) use ($cb, $batchSize, $join, $where, &$lastId, &$finished) {
                    $qnrRes = $this->entityManager->createQuery(/** @lang DQL */ "
                        SELECT qnr
                        FROM App\Entity\ResidentQuestionnaire qnr $join
                        WHERE qnr.id > :lastId AND $where
                        ORDER BY qnr.id
                    ")->setMaxResults($batchSize)->setParameter('lastId', $lastId)->getResult();
                    if (count($qnrRes) > 0) {
                        $prevLastId = $lastId;
                        $lastId = $qnrRes[count($qnrRes) - 1]->getId();
                        $cb($qnrRes, $prevLastId, $lastId);
                    } else {
                        $cb([], $lastId, null);
                        $finished = true;
                    }
                }
            );
            // clear сильно ускоряет работу, если анкет очень много
            $this->entityManager->clear();
        }
    }

    /**
     * @param ClientFormResponse[] $array
     * @return array
     */
    protected function mapByQnrId($array)
    {
        $res = [];
        foreach ($array as $cfr) {
            $res[$cfr->getResidentQuestionnaireId()] = $cfr;
        }
        return $res;
    }
}
