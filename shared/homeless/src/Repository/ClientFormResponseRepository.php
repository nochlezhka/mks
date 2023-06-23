<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientForm;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponse;
use App\Entity\ClientFormResponseValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientFormResponse|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientFormResponse|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientFormResponse> findAll()
 * @method array<ClientFormResponse> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFormResponseRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly ClientFormFieldRepository $clientFormFieldRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientFormResponse::class);
    }

    /**
     * Подготовка заполненной анкеты к сохранению в базе:
     * * Преобразование полей из `_submittedFields`, которые пришли из формы, в объекты `ClientFormResponseValue`;
     * * Выставление системных полей в объектах анкеты и значений полей анкеты.
     */
    public function prepareForCreateOrUpdate(ClientFormResponse $object, ClientForm $currentForm): void
    {
        $submittedFields = $object->_getSubmittedFields();
        $toRemove = [];
        $object->setForm($currentForm);

        // обновляем значения уже привязанных к анкете полей
        // после этого в `$submittedFields` останутся только значения новых полей
        // айдишники поле со значением `null` будут добавлены на удаление в `$toRemove`
        /** @var ClientFormResponseValue $fieldValue */
        foreach ($object->getValues() as $fieldValue) {
            $fieldId = $fieldValue->getClientFormField()->getId();
            if (!\array_key_exists($fieldId, $submittedFields)) {
                continue;
            }

            $value = $submittedFields[$fieldId];
            if ($value === null) {
                $toRemove[] = $fieldId;
            } else {
                $fieldValue->setValue((string) $value);
            }
            unset($submittedFields[$fieldId]);
        }

        if (\count($toRemove) > 0) {
            $idsMap = array_fill_keys($toRemove, 1);

            $formValues = $object->getValues();
            for ($i = \count($formValues) - 1; $i >= 0; --$i) {
                $fieldValue = $formValues[$i];
                $fieldId = $fieldValue->getClientFormField()->getId();
                if (\array_key_exists($fieldId, $idsMap)) {
                    $object->getValues()->removeElement($fieldValue);
                }
            }
        }

        // если есть новые поля, привязываем их
        // у новой анкеты все поля - новые, т.к. привязанных ещё не было
        if (\count($submittedFields) <= 0) {
            return;
        }

        $fields = $this->clientFormFieldRepository->findBy([
            'id' => array_keys($submittedFields),
        ]);
        $fieldsById = [];
        /**
         * @var ClientFormField $field
         */
        foreach ($fields as $field) {
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
            $fieldValue->setValue((string) $value);

            $formValues[] = $fieldValue;
        }
        $object->setValues($formValues);
    }
}
