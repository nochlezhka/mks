<?php /** @noinspection PhpUnused */


namespace App\Command;


use App\Entity\ResidentQuestionnaire;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResidentQnrCopyCommand extends ResidentQnrCommon
{
    protected function configure()
    {
        $this->setName('homeless:resident_qnr:copy')
            ->setDescription("Скопировать анкеты проживающего в новый формат (ClientFormResponse)");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initDependencies();
        $this->initLogging($output, "copy");

        if (!$this->checkEnabled() || !$this->checkClientFormSchema()) {
            return 1;
        }

        $this->logger->info("started copying");
        $this->batchEnumQnr([$this, 'processQnrArray'], "
            LEFT JOIN App\Entity\ClientFormResponse cfr WITH cfr.residentQuestionnaireId = qnr.id",
            "cfr.id IS NULL"
        );
        $this->logger->info("finished copying");

        return 0;
    }

    /**
     * Копирует анкеты проживающего из массива `$qnrArray` в новый формат.
     *
     * @param ResidentQuestionnaire[] $qnrArray массив анкет проживающего
     * @throws TransactionRequiredException
     */
    protected function processQnrArray(array $qnrArray, $idFrom, $idUntil)
    {
        if (count($qnrArray) == 0) {
            return;
        }
        $this->logger->info("copying range ($idFrom," . ($idUntil === null ? "inf)" : "$idUntil]"));
        $qnrIdsDql = join(',', array_map(function (ResidentQuestionnaire $qnr) {
            return $this->entityManager->getConnection()->quote($qnr->getId());
        }, $qnrArray));

        $existingCfr = $this->entityManager->createQuery(/* @lang DQL */ "
            SELECT cfr.id
            FROM App\Entity\ClientFormResponse cfr
            WHERE (cfr.residentQuestionnaireId IN ($qnrIdsDql))
        ")->setLockMode(LockMode::PESSIMISTIC_WRITE)->getResult();
        $existingCfrById = $this->mapByQnrId($existingCfr);

        foreach ($qnrArray as $qnr) {
            if (isset($existingCfrById[$qnr->getId()])) {
                $this->logger->info("skipping resident qnr " . $qnr->getId());
                continue;
            }
            /**
             * @var ResidentQuestionnaire $qnr
             */
            $this->residentQuestionnaireConverter->createOrUpdateClientFormResponse($qnr, null);
        }
    }
}
