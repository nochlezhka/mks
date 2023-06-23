<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ClientForm;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponse;
use App\Form\DataTransformer\ClientFormCheckboxTransformer;
use App\Form\DataTransformer\ClientFormMultiselectTransformer;
use App\Util\BaseEntityUtil;
use App\Util\ClientFormUtil;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ClientFormResponseAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

    /**
     * ID формы. Дочерний класс-админка может выставить ID здесь, тогда
     * все методы админки будут понимать, из какой формы составлять анкету.
     *
     * @see ResidentFormResponseAdmin
     */
    protected ?int $formId;

    /**
     * Создание анкеты. Оборачиваем в транзакцию, т.к. нужно консистентно писать в несколько таблиц.
     */
    public function prePersist(object $object): void
    {
        if (!$object instanceof ClientFormResponse) {
            return;
        }

        $this->entityManager->wrapInTransaction(function (EntityManager $em) use ($object): void {
            /** @var \App\Repository\ClientFormResponseRepository $repository */
            $repository = $em->getRepository(ClientFormResponse::class);
            $repository->prepareForCreateOrUpdate($object, $this->getCurrentForm());
        });
    }

    /**
     * Обновление анкеты. Оборачиваем в транзакцию для консистентности таблицы `_value`
     * и лочимся об запись в `client_form_response`, чтобы случайно не смешать несколько параллельных обновлений.
     */
    public function preUpdate(object $object): void
    {
        if (!$object instanceof ClientFormResponse) {
            return;
        }

        $this->entityManager->wrapInTransaction(function (EntityManager $em) use ($object): void {
            /** @var \App\Repository\ClientFormResponseRepository $repository */
            $repository = $em->getRepository(ClientFormResponse::class);
            $em->lock($object, LockMode::PESSIMISTIC_WRITE);
            $repository->prepareForCreateOrUpdate($object, $this->getCurrentForm());
        });
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        // если дочерняя админка хардкодит ID формы, добавляем его в качестве неявного фильтра по-умолчанию
        if ($this->formId !== null) {
            $filterValues['form'] = [
                'value' => $this->formId,
            ];
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        // если дочерняя админка хардкодит ID формы, добавляем его в качестве скрытого фильтра
        if ($this->formId !== null) {
            $filter->add('form', null, [
                'show_filter' => false,
                'label' => false,
                'field_type' => ModelHiddenType::class,
                'field_options' => [
                    'model_manager' => $this->getModelManager(),
                ],
                'operator_type' => HiddenType::class,
            ]);
        }
    }

    protected function configureFormFields(FormMapper $form): void
    {
        // из полей текущей формы составляем фейковые поля админки
        // значения этих полей будут читаться и сохраняться в объекте ClientFormResponse через магические методы
        /** @var array<ClientFormField> $formFields */
        $formFields = $this->getCurrentForm()->getFields()->toArray();
        BaseEntityUtil::sortEntities($formFields);

        /** @var ClientFormResponse $subject */
        $subject = $this->getSubject();
        $formBuilder = $form->getFormBuilder();

        foreach ($formFields as $field) {
            $fieldName = 'field_'.$field->getId();
            $fieldDesc = $this->getFieldDescription($field, $fieldName, $subject);
            $form->add($fieldName, $fieldDesc['type'], $fieldDesc['options']);
            if ($fieldDesc['type'] === CheckboxType::class) {
                // для полей-чекбоксов ещё навешиваем преобразователь типов, т.к. у всех полей значения строкового типа.
                $formBuilder->get($fieldName)->addModelTransformer(new ClientFormCheckboxTransformer($subject->getId(), $field->getId()));
            } elseif ($fieldDesc['type'] === ChoiceType::class && ($fieldDesc['options']['multiple'] ?? false)) {
                // для селектов со множественным выбором навешиваем преобразователь типов из строк в массив строк и обратно
                $formBuilder->get($fieldName)->addModelTransformer(new ClientFormMultiselectTransformer($subject->getId(), $field->getId()));
            }
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $firstField = $this->getCurrentForm()->getFirstField();
        if ($firstField === null) {
            return;
        }
        $list
            ->addIdentifier('firstFieldValue', null, [
                'label' => $firstField->getName(),
            ])
            ->add('isFull', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Заполнено',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * Возвращает объект формы, из которой будет составлена анкета.
     * ID формы берётся либо из `$this->formId`, либо из GET параметра `form_id`.
     * Или из текущей редактируемой анкеты.
     */
    private function getCurrentForm(): ClientForm
    {
        if (!$this->hasSubject()) {
            $this->setSubject($this->getNewInstance());
        }

        /** @var ClientFormResponse $subject */
        $subject = $this->getSubject();
        if ($this->formId !== null) {
            $formId = $this->formId;
        } elseif ($subject->getForm() !== null) {
            return $subject->getForm();
        } else {
            $formId = $this->getRequest()->get('form_id');
            if (!$formId) {
                throw new BadRequestHttpException('form_id must be set');
            }
        }
        /** @var ClientForm $currentForm */
        $currentForm = $this->getModelManager()->find(ClientForm::class, $formId);
        if (!$currentForm) {
            throw new BadRequestHttpException('ClientForm '.$formId.' was not found');
        }

        return $currentForm;
    }

    /**
     * По объекту поля формы определяет, какие параметры нужно передать в `FormMapper` админки.
     * Возвращает массив с полями `type` и `options` для метода `$formMapper->add()`
     *
     * @param string $fieldName
     */
    private function getFieldDescription(ClientFormField $field, $fieldName, ClientFormResponse $subject): array
    {
        $options = [
            'label' => $field->getName(),
            'required' => $field->isRequired(),
        ];
        switch ($field->getType()) {
            case ClientFormField::TYPE_TEXT:
                $type = TextType::class;
                break;

            case ClientFormField::TYPE_OPTION:
                $type = ChoiceType::class;
                $optionsText = $field->getOptions();
                $choiceList = ClientFormUtil::optionsTextToArray($optionsText);
                $options['choices'] = array_combine($choiceList, $choiceList);
                // если в редактируемой анкете у этого поля выставлено значение, которого нет в списке для выбора,
                // пишем об этом в лог и добавляем значение в список
                if ($subject?->getId() !== null) {
                    $value = $subject->__get($fieldName);
                    $fieldValues = [];
                    if ($value !== null) {
                        $fieldValues = $field->isMultiselect() ? ClientFormUtil::optionsTextToArray($value) : [$value];
                    }
                    foreach ($fieldValues as $fieldValue) {
                        if (!\array_key_exists($fieldValue, $options['choices'])) {
                            error_log('Missing choice '.($fieldValue === null ? 'null' : "'{$fieldValue}'").' in field '.$fieldName.' of client form response '.$subject->getId());
                            $options['choices'][$fieldValue] = $fieldValue;
                        }
                    }
                }
                if ($field->isMultiselect()) {
                    $options['multiple'] = true;
                }
                break;

            case ClientFormField::TYPE_CHECKBOX:
                $type = CheckboxType::class;
                break;

            default:
                error_log('Unknown client form field type '.$field->getType());
                $type = TextType::class;
                break;
        }

        return [
            'type' => $type,
            'options' => $options,
        ];
    }
}
