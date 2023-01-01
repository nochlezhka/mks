<?php


namespace App\Service;


use App\Entity\ClientForm;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponse;
use App\Entity\ResidentQuestionnaire;
use App\Repository\ClientFormRepository;
use App\Repository\ClientFormResponseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

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
     * @var array
     */
    private $residentQrnFormSchemaCache;

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

    private function getResidentQnrFormSchema()
    {
        if ($this->residentQrnFormSchemaCache !== null) {
            return $this->residentQrnFormSchemaCache;
        }
        $this->residentQrnFormSchemaCache = [
            'type' => [
                'getter' => 'getTypeId',
                'type' => ClientFormField::TYPE_OPTION,
                'options' => ResidentQuestionnaire::$types,
                'fieldId' => 1,
            ],
            'isDwelling' => [
                'getter' => 'getisDwelling',
                'type' => ClientFormField::TYPE_CHECKBOX,
                'fieldId' => 2,
            ],
            'roomType' => [
                'getter' => 'getRoomTypeId',
                'type' => ClientFormField::TYPE_OPTION,
                'options' => ResidentQuestionnaire::$roomTypes,
                'fieldId' => 3,
            ],
            'isWork' => [
                'getter' => 'getisWork',
                'type' => ClientFormField::TYPE_CHECKBOX,
                'fieldId' => 4,
            ],
            'isWorkOfficial' => [
                'getter' => 'getisWorkOfficial',
                'type' => ClientFormField::TYPE_CHECKBOX,
                'fieldId' => 5,
            ],
            'isWorkConstant' => [
                'getter' => 'getisWorkConstant',
                'type' => ClientFormField::TYPE_CHECKBOX,
                'fieldId' => 6,
            ],
            'changedJobsCount' => [
                'getter' => 'getChangedJobsCountId',
                'type' => ClientFormField::TYPE_OPTION,
                'options' => ResidentQuestionnaire::$changedJobsCounts,
                'fieldId' => 7,
            ],
            'reasonsForTransition' => [
                'getter' => 'getReasonForTransitionIds',
                'type' => ClientFormField::TYPE_OPTION,
                'multiselect' => 1,
                'options' => ResidentQuestionnaire::$reasonForTransitions,
                'fieldId' => 8,
            ],
            'reasonsForPetition' => [
                'getter' => 'getReasonForPetitionIds',
                'type' => ClientFormField::TYPE_OPTION,
                'multiselect' => 1,
                'options' => ResidentQuestionnaire::$reasonForPetition,
                'fieldId' => 9,
            ],
        ];
        return $this->residentQrnFormSchemaCache;
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
        $res = $this->entityManager->createQuery(/* @lang DQL */ "
            SELECT cfr FROM App\Entity\ClientFormResponse cfr
            WHERE cfr.residentQuestionnaireId = :qnrId
        ")->setParameter('qnrId', $qnr->getId())->setLockMode(LockMode::PESSIMISTIC_WRITE)->getResult();
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
        $formSchema = $this->getResidentQnrFormSchema();
        foreach ($formSchema as $field => $fieldSchema) {
            $value = $this->extractValueFromResidentQnr($qnr, $field, $fieldSchema);
            $fieldId = $fieldSchema['fieldId'];
            $resp->__set("field_$fieldId", $value);
        }
        $qnrForm = $this->clientFormRepository->find(ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID);
        /**
         * @var $qnrForm ClientForm
         */
        $this->clientFormResponseRepository->prepareForCreateOrUpdate($resp, $qnrForm);
        $this->entityManager->persist($resp);
    }

    private function extractValueFromResidentQnr(ResidentQuestionnaire $qnr, $field, $fieldSchema)
    {
        $getter = $fieldSchema['getter'];
        $qnrValue = $qnr->$getter();
        $value = null;
        switch ($fieldSchema['type']) {
            case ClientFormField::TYPE_CHECKBOX:
                $value = self::convertBoolean($qnrValue);
                break;
            case ClientFormField::TYPE_OPTION:
                $options = $fieldSchema['options'];
                if (isset($fieldSchema['multiselect']) && $fieldSchema['multiselect']) {
                    $value = self::convertMultiselect($qnrValue, $options, $field);
                } else {
                    $value = self::convertSelect($qnrValue, $options, $field);
                }
                break;
            default:
                throw new \LogicException("Didn't expect fields of type ".$fieldSchema['type']." ($field)");
        }
        return $value;
    }

    public static function convertBoolean($value)
    {
        return $value ? '1' : null;
    }

    public static function convertSelect($value, $mapping, $name)
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

    public static function convertMultiselect($values, $mapping, $name)
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
     * Удаляет копию анкеты проживающего, составленную из `$qnr`, в нофом формате.
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

    public function checkClientFormSchema(LoggerInterface $logger)
    {
        $qnrSchema = $this->getResidentQnrFormSchema();
        $fieldIdToType = [];
        foreach ($qnrSchema as $field => $fieldSchema) {
            $fieldIdToType[$fieldSchema['fieldId']] = $fieldSchema['type'];
        }

        $qnrForm = $this->getResidentQnrClientForm();
        $formFields = $qnrForm->getFields();
        $hasErrors = false;
        foreach ($formFields as $field) {
            /**
             * @var ClientFormField $field
             */
            $id = $field->getId();
            if (!isset($fieldIdToType[$id])) {
                continue;
            }
            $expectedType = $fieldIdToType[$id];
            if ($field->getType() != $expectedType) {
                $logger->error("Field ".$field->getName()." has wrong type ".$field->getType().
                    " (expected $expectedType)");
                $hasErrors = true;
            }
            unset($fieldIdToType[$id]);
        }
        if (count($fieldIdToType) > 0) {
            $logger->error("Fields ".join(', ', array_keys($fieldIdToType))." were not found.");
            $hasErrors = true;
        }

        return !$hasErrors;
    }

    public function residentQnrToArray(ResidentQuestionnaire $qnr)
    {
        $schema = $this->getResidentQnrFormSchema();
        $array = [];
        foreach ($schema as $field => $fieldSchema) {
            $array[$field] = $this->extractValueFromResidentQnr($qnr, $field, $fieldSchema);
        }
        return $array;
    }

    public function residentQnrClientFormToArray(ClientFormResponse $cfr)
    {
        $schema = $this->getResidentQnrFormSchema();
        $array = [];
        foreach ($schema as $field => $fieldSchema) {
            $fieldId = $fieldSchema['fieldId'];
            $array[$field] = $cfr->__get("field_$fieldId");
        }
        return $array;
    }

    /**
     * @return ClientForm
     */
    private function getResidentQnrClientForm()
    {
        return $this->clientFormRepository->find(ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID);
    }
}
