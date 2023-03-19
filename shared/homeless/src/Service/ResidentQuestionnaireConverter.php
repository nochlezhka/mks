<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Service;

use App\Entity\ClientForm;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponse;
use App\Entity\ResidentQuestionnaire;
use App\Repository\ClientFormRepository;
use App\Repository\ClientFormResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ResidentQuestionnaireConverter
{
    private ?array $residentQrnFormSchemaCache;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ClientFormResponseRepository $clientFormResponseRepository,
        private readonly ClientFormRepository $clientFormRepository,
    ) {}

    public static function convertBoolean($value): ?string
    {
        return $value ? '1' : null;
    }

    public static function convertSelect($value, $mapping, $name): mixed
    {
        if ($value === null) {
            return null;
        }
        if (isset($mapping[$value])) {
            return $mapping[$value];
        }
        error_log("ResidentQuestionnaireConverter::convertSelect: value {$value} was not found in mapping for {$name}");

        return $value;
    }

    public static function convertMultiselect($values, $mapping, $name): ?string
    {
        if (!\is_array($values)) {
            return null;
        }
        $values = array_filter($values, static fn ($v) => $v !== '');
        if (\count($values) === 0) {
            return null;
        }
        $textValues = array_map(
            static function ($val) use ($mapping, $name) {
                if (isset($mapping[$val])) {
                    return $mapping[$val];
                }
                error_log("ResidentQuestionnaireConverter::convertMultiselect: value {$val} was not found in mapping for {$name}");

                return $val;
            },
            $values,
        );

        return implode("\n", $textValues);
    }

    /**
     * Если параметр `$resp` не `null`, обновляет копию анкеты `$resp` из анкеты в старом формате `$qnr`
     * Если `$resp` - `null`, создаёт новую копию анкеты на основе значений из `$qnr`
     */
    public function createOrUpdateClientFormResponse(ResidentQuestionnaire $qnr, ?ClientFormResponse $resp): void
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
            $resp->__set("field_{$fieldId}", $value);
        }
        $qnrForm = $this->clientFormRepository->find(ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID);
        /**
         * @var ClientForm $qnrForm
         */
        $this->clientFormResponseRepository->prepareForCreateOrUpdate($resp, $qnrForm);
        $this->entityManager->persist($resp);
    }

    public function checkClientFormSchema(LoggerInterface $logger): bool
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
            if ($field->getType() !== $expectedType) {
                $logger->error('Field '.$field->getName().' has wrong type '.$field->getType().
                    " (expected {$expectedType})");
                $hasErrors = true;
            }
            unset($fieldIdToType[$id]);
        }
        if (\count($fieldIdToType) > 0) {
            $logger->error('Fields '.implode(', ', array_keys($fieldIdToType)).' were not found.');
            $hasErrors = true;
        }

        return !$hasErrors;
    }

    public function residentQnrToArray(ResidentQuestionnaire $qnr): array
    {
        $schema = $this->getResidentQnrFormSchema();
        $array = [];
        foreach ($schema as $field => $fieldSchema) {
            $array[$field] = $this->extractValueFromResidentQnr($qnr, $field, $fieldSchema);
        }

        return $array;
    }

    public function residentQnrClientFormToArray(ClientFormResponse $cfr): array
    {
        $schema = $this->getResidentQnrFormSchema();
        $array = [];
        foreach ($schema as $field => $fieldSchema) {
            $fieldId = $fieldSchema['fieldId'];
            $array[$field] = $cfr->__get("field_{$fieldId}");
        }

        return $array;
    }

    private function getResidentQnrFormSchema(): ?array
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

    private function extractValueFromResidentQnr(ResidentQuestionnaire $qnr, $field, $fieldSchema): mixed
    {
        $getter = $fieldSchema['getter'];
        $qnrValue = $qnr->{$getter}();
        switch ($fieldSchema['type']) {
            case ClientFormField::TYPE_CHECKBOX:
                $value = self::convertBoolean($qnrValue);
                break;

            case ClientFormField::TYPE_OPTION:
                $options = $fieldSchema['options'];
                $value = isset($fieldSchema['multiselect']) && $fieldSchema['multiselect'] ? self::convertMultiselect($qnrValue, $options, $field) : self::convertSelect($qnrValue, $options, $field);
                break;

            default:
                throw new \LogicException("Didn't expect fields of type ".$fieldSchema['type']." ({$field})");
        }

        return $value;
    }

    private function getResidentQnrClientForm(): ClientForm
    {
        return $this->clientFormRepository->find(ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID);
    }
}
