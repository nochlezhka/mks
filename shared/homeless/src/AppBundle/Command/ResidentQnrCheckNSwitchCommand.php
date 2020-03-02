<?php /** @noinspection PhpUnused */


namespace AppBundle\Command;


use AppBundle\Entity\BaseEntity;
use AppBundle\Entity\ResidentQuestionnaire;
use Application\Sonata\UserBundle\Entity\Group;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResidentQnrCheckNSwitchCommand extends ResidentQnrCommon
{
    private $foundErrors = false;

    protected function configure()
    {
        $this->setName('homeless:resident_qnr:check_and_switch')
            ->setDescription("Проверить, что анкеты проживающего скопировались в новый формат и включить")
            ->addOption("switch", null, InputOption::VALUE_NONE,
                "Включить анкеты проживающего в новом формате");
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
        $this->initLogging($output, "check_and_switch");

        if (!$this->checkEnabled() || !$this->checkClientFormSchema()) {
            return 1;
        }

        $this->logger->info("started checking copies");

        $this->checkResidentQnrCopies();
        if ($this->foundErrors) {
            $this->logger->error("found errors");
            return 1;
        } else {
            $this->logger->info("no errors found");
        }

        if ($input->getOption('switch')) {
            $this->logger->info("switching system to new forms");
            $this->entityManager->transactional(
                function (EntityManager $em) {
                    $this->metaService->enableClientForms();
                    $this->switchGroupRoles();
                    $em->flush();
                }
            );
        } else {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln("Проверка копий всех анкет проживающего прошла успешно.");
                $output->writeln("Запустите команду с флагом --switch чтобы включить анкеты в новом формате.");
            }
        }

        $this->logger->info("finished copy check");
        return 0;
    }

    /**
     * @throws Exception
     */
    private function checkResidentQnrCopies()
    {
        $this->batchEnumQnr([$this, 'processQnrArray']);
    }

    /**
     * Проверяет, есть ли точные копии анкет проживающего из массива `$qnrArray` в заданном диапазоне айдишников.
     * Диапазон нужен, чтобы было легче найти копии-сироты, когда оригинальная анкета была удалена, а копия осталась.
     *
     * @param ResidentQuestionnaire[] $qnrArray массив анкет проживающего
     * @param int $idFrom id анкеты с которого начинается проверяемый диапазон, не включительно
     * @param int|null $idUntil id последней анкеты в проверяемом диапазоне.
     *                          Или null, если нужно проверить все оставшиеся айдишники
     */
    protected function processQnrArray(array $qnrArray, $idFrom, $idUntil)
    {
        $this->logger->info("checking range ($idFrom," . ($idUntil === null ? "inf)" : "$idUntil]"));

        $qnrRangeConditionDql = 'cfr.residentQuestionnaireId > :idFrom';
        $queryParams = ['idFrom' => $idFrom];
        if ($idUntil !== null) {
            $qnrRangeConditionDql .= ' AND cfr.residentQuestionnaireId <= :idUntil';
            $queryParams['idUntil'] = $idUntil;
        }
        // джойн cfr.values полезен, чтобы одним запросом выбрать поля анкет в новом формате
        $cfrRes = $this->entityManager->createQuery(/* @lang DQL */ "
            SELECT cfr, values
            FROM AppBundle\Entity\ClientFormResponse cfr
            JOIN cfr.values values
            WHERE $qnrRangeConditionDql
        ")->setParameters($queryParams)->getResult();

        $qnrById = $this->mapById($qnrArray);
        $cfrByQnrId = $this->mapByQnrId($cfrRes);
        foreach ($qnrById as $id => $qnr) {
            if (!isset($cfrByQnrId[$id])) {
                $this->logger->error("no copy found for resident questionnaire $id");
                $this->foundErrors = true;
                continue;
            }
            $qnrData = $this->residentQuestionnaireConverter->residentQnrToArray($qnr);
            $cfrData = $this->residentQuestionnaireConverter->residentQnrClientFormToArray($cfrByQnrId[$id]);
            if (!$this->cmpForms($qnrData, $cfrData)) {
                $qnrJson = json_encode($qnrData);
                $cfrJson = json_encode($cfrData);
                $this->logger->error("resident questionnaire $id and its copy are different. " .
                    "Original: $qnrJson, Copy: $cfrJson"
                );
                $this->foundErrors = true;
            }
            // отмечаем, что для копии с таким айдишником анкеты был найден оригинал
            unset($cfrByQnrId[$id]);
        }
        if (count($cfrByQnrId) > 0) {
            $this->logger->error("found orphan resident questionnaire copies for ids " .
                join(',', array_keys($cfrByQnrId)));
            $this->foundErrors = true;
        }
    }

    /**
     * @param BaseEntity[] $array
     * @return array
     */
    private function mapById($array)
    {
        $res = [];
        foreach ($array as $baseEntity) {
            $res[$baseEntity->getId()] = $baseEntity;
        }
        return $res;
    }

    /**
     * Сравнивает два массива с данными анкеты и её копии.
     *
     * @param array $qnrData данные анкеты
     * @param array $cfrData данные копии анкеты
     * @return bool false, если анкеты различаются
     */
    private function cmpForms(array $qnrData, array $cfrData)
    {
        foreach ($qnrData as $key => $value) {
            if ($qnrData[$key] !== $cfrData[$key]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Отнимает у всех групп роли "ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_*",
     * а взамен выдаёт симметричные роли "ROLE_APP_RESIDENT_FORM_RESPONSE_ADMIN_*"
     *
     * Так у них пропадёт доступ к старым анкетам и появится такой же доступ к новым анкетам.
     */
    private function switchGroupRoles()
    {
        $groupRepo = $this->entityManager->getRepository(Group::class);
        $groups = $groupRepo->findAll();
        foreach ($groups as $group) {
            /**
             * @var Group $group
             */
            $qnrRoles = array_filter(
                $group->getRoles(),
                function ($role) {
                    return strpos($role, "ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_") !== false;
                }
            );
            if (count($qnrRoles)) {
                $groupName = $group->getName();
                $this->logger->info("old roles of group $groupName", $group->getRoles());
                $this->logger->info("resident questionnaire admin roles for group $groupName:", $qnrRoles);
                $filteredOldRoles = array_filter(
                    $group->getRoles(),
                    function ($role) {
                        return strpos($role, "ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_") === false;
                    }
                );
                $newRoles = array_map(
                    function ($role) {
                        return str_replace(
                            "ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_",
                            "ROLE_APP_RESIDENT_FORM_RESPONSE_ADMIN_",
                            $role
                        );
                    },
                    $qnrRoles
                );
                $this->logger->info("giving new roles to group $groupName:", $newRoles);
                $group->setRoles(array_merge($filteredOldRoles, $newRoles));
                $this->logger->info("new roles of group $groupName", $group->getRoles());
            }
        }
    }
}
