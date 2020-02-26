<?php


namespace AppBundle\Service;


use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormResponse;
use AppBundle\Entity\ResidentQuestionnaire;
use AppBundle\Repository\ClientFormRepository;
use AppBundle\Repository\ClientFormResponseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;

class ResidentQuestionnaireConverter
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ClientFormResponseRepository
     */
    private $clientFormResponseRepository;

    /**
     * @var ClientFormRepository
     */
    private $clientFormRepository;

    /**
     * ResidentQuestionnaireConverter constructor.
     * @param ClientFormResponseRepository $clientFormResponseRepository
     */
    public function __construct(
        EntityManager $entityManager,
        ClientFormResponseRepository $clientFormResponseRepository,
        ClientFormRepository $clientFormRepository
    ) {
        $this->entityManager = $entityManager;
        $this->clientFormResponseRepository = $clientFormResponseRepository;
        $this->clientFormRepository = $clientFormRepository;
    }

    /**
     * Лочит в БД копию анкеты проживающего `$qnr`.
     * Если копия не найдена, возвращает `null`.
     *
     * @param ResidentQuestionnaire $qnr
     * @return ClientFormResponse|null
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function lockClientForm(ResidentQuestionnaire $qnr)
    {
        $q = $this->entityManager->createQuery("
            SELECT cfr FROM ".ClientFormResponse::class." cfr
            WHERE cfr.residentQuestionnaireId = :qnrId");
        $q->setParameter('qnrId', $qnr->getId());
        $q->setLockMode(LockMode::PESSIMISTIC_WRITE);
        $res = $q->getResult();
        return count($res) > 0 ? $res[0] : null;
    }

    /**
     * Если параметр `$resp` не `null`, обновляет копию анкеты `$resp` из анкеты в старом формате `$qnr`
     * Если `$resp` - `null`, создаёт новую копию анкеты на основе значений из `$qnr`
     *
     * @param ResidentQuestionnaire $qnr
     * @param ClientFormResponse|null $resp
     */
    public function createOrUpdateClientFormResponse(ResidentQuestionnaire $qnr, $resp)
    {
        if ($resp === null) {
            $resp = new ClientFormResponse();
            $resp->setClient($qnr->getClient());
            $resp->setResidentQuestionnaireId($qnr->getId());
        }
        $resp->__set('field_1', $this->convertSelect($qnr->getTypeId(), ResidentQuestionnaire::$types, 'type'));
        $resp->__set('field_2', $this->convertBoolean($qnr->getisDwelling()));
        $resp->__set('field_3', $this->convertSelect($qnr->getRoomTypeId(), ResidentQuestionnaire::$roomTypes, 'roomType'));
        $resp->__set('field_4', $this->convertBoolean($qnr->getisWork()));
        $resp->__set('field_5', $this->convertBoolean($qnr->getisWorkOfficial()));
        $resp->__set('field_6', $this->convertBoolean($qnr->getisWorkConstant()));
        $resp->__set('field_7', $this->convertSelect($qnr->getChangedJobsCountId(), ResidentQuestionnaire::$changedJobsCounts, 'changedJobsCount'));
        $resp->__set('field_8', $this->convertMultiselect($qnr->getReasonForTransitionIds(), ResidentQuestionnaire::$reasonForTransitions, 'reasonsForTransition'));
        $resp->__set('field_9', $this->convertMultiselect($qnr->getReasonForPetitionIds(), ResidentQuestionnaire::$reasonForPetition, 'reasonsForPetition'));
        $qnrForm = $this->clientFormRepository->find(ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID);
        /**
         * @var $qnrForm ClientForm
         */
        $this->clientFormResponseRepository->prepareForCreateOrUpdate($resp, $qnrForm);
        $this->entityManager->persist($resp);
    }

    private function convertBoolean($value)
    {
        return $value ? '1' : null;
    }

    private function convertSelect($value, $mapping, $name)
    {
        if ($value === null) {
            return null;
        }
        if (isset($mapping[$value])) {
            return $mapping[$value];
        }
        error_log("ResidentQuestionnaireConverter::convertSelect: value $value was not found in mapping for $name");
        return $value;
    }

    private function convertMultiselect($values, $mapping, $name)
    {
        if (!is_array($values)) {
            return null;
        }
        $values = array_filter($values, function ($v) { return $v !== ''; });
        if (count($values) == 0) {
            return null;
        }
        $textValues = array_map(
            function($val) use($mapping, $name) {
                if (isset($mapping[$val])) {
                    return $mapping[$val];
                }
                error_log("ResidentQuestionnaireConverter::convertMultiselect: value $val was not found in mapping for $name");
                return $val;
            },
            $values
        );
        return implode("\n", $textValues);
    }

    /**
     * Удаляет копию анкеты проживавшего, составленную из `$qnr`, в нофом формате.
     *
     * @param ResidentQuestionnaire $qnr
     */
    public function deleteClientFormResponse(ResidentQuestionnaire $qnr)
    {
        $resp = $this->clientFormResponseRepository->findOneBy(['residentQuestionnaireId' => $qnr->getId()]);
        if ($resp !== null) {
            $this->entityManager->remove($resp);
        }
    }
}
