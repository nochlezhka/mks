<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponse;
use AppBundle\Entity\ClientFormResponseValue;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;

/**
 * Class ClientFormResponseRepository
 * @package AppBundle\Repository
 */
class ClientFormResponseRepository extends EntityRepository
{
    /**
     * Подготовка заполненной анкеты к сохранению в базе:
     * * Преобразование полей из `_submittedFields`, которые пришли из формы, в объекты `ClientFormResponseValue`;
     * * Выставление системных полей в объектах анкеты и значений полей анкеты.
     *
     * @param ClientFormResponse $object
     * @param ClientForm $currentForm
     */
    public function prepareForCreateOrUpdate(ClientFormResponse $object, ClientForm $currentForm)
    {
        $submittedFields = $object->_getSubmittedFields();
        $toRemove = [];
        $object->setForm($currentForm);

        // обновляем значения уже привязанных к анкете полей
        // после этого в `$submittedFields` останутся только значения новых полей
        // айдишники поле со значением `null` будут добавлены на удаление в `$toRemove`
        foreach ($object->getValues() as $fieldValue) {
            /**
             * @var $fieldValue ClientFormResponseValue
             */
            $fieldId = $fieldValue->getClientFormField()->getId();
            if (array_key_exists($fieldId, $submittedFields)) {
                $value = $submittedFields[$fieldId];
                if ($value === null) {
                    $toRemove[] = $fieldId;
                } else {
                    $fieldValue->setValue($value);
                }
                unset($submittedFields[$fieldId]);
            }
        }

        if (count($toRemove) > 0) {
            $idsMap = array_fill_keys($toRemove, 1);

            $formValues = $object->getValues();
            for ($i = count($formValues) - 1; $i >= 0; --$i) {
                $fieldValue = $formValues[$i];
                $fieldId = $fieldValue->getClientFormField()->getId();
                if (array_key_exists($fieldId, $idsMap)) {
                    $object->getValues()->removeElement($fieldValue);
                }
            }
        }

        // если есть новые поля, привязываем их
        // у новой анкеты все поля - новые, т.к. привязанных ещё не было
        if (count($submittedFields) > 0) {
            $fields = $this->getEntityManager()->getRepository(ClientFormField::class)->findBy([
                'id' => array_keys($submittedFields)
            ]);
            $fieldsById = [];
            foreach ($fields as $field) {
                /**
                 * @var $field ClientFormField
                 */
                $fieldsById[$field->getId()] = $field;
            }

            $formValues = $object->getValues();
            foreach ($submittedFields as $fieldId => $value) {
                if ($value === null) {
                    continue;
                }
                $fieldValue = new ClientFormResponseValue();
                $fieldValue->setClientFormResponse($object);
                $fieldValue->setClientFormField($fieldsById[$fieldId]);
                $fieldValue->setClient($object->getClient());
                $fieldValue->setValue($value);

                $formValues[] = $fieldValue;
            }
            $object->setValues($formValues);
        }
    }

    public function lockForUpdate(ClientFormResponse $cfr)
    {
        $this->find($cfr->getId(), LockMode::PESSIMISTIC_WRITE);
    }
}
