<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponse;
use AppBundle\Entity\ClientFormResponseValue;
use AppBundle\Form\DataTransformer\ClientFormCheckboxTransformer;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ClientFormResponseAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $parentAssociationMapping = 'client';

    protected $translationDomain = 'AppBundle';

    /**
     * ID формы. Дочерний класс-админка может выставить ID здесь, тогда
     * все методы админки будут понимать, из какой формы составлять анкету.
     *
     * @see ResidentFormResponseAdmin
     * @var integer|null
     */
    protected $formId = null;

    protected function configureDefaultFilterValues(array &$filterValues)
    {
        // если дочерняя админка хардкодит ID формы, добавляем его в качестве неявного фильтра по-умолчанию
        if ($this->formId != null) {
            $filterValues['form'] = [
                'type' => EqualType::TYPE_IS_EQUAL,
                'value' => $this->formId,
            ];
        }
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        // если дочерняя админка хардкодит ID формы, добавляем его в качестве скрытого фильтра
        if ($this->formId != null) {
            $datagridMapper
                ->add('form', null, array(
                    'show_filter' => false,
                    'label' => false,
                    'field_type' => ModelHiddenType::class,
                    'field_options' => array(
                        'model_manager' => $this->getModelManager(),
                    ),
                    'operator_type' => HiddenType::class,
                ));
        }
    }

    /**
     * Возвращает объект формы, из которой будет составлена анкета.
     * ID формы берётся либо из `$this->formId`, либо из GET параметра `form_id`.
     * Или из текущей редактируемой анкеты.
     *
     * @return ClientForm
     */
    private function getCurrentForm()
    {
        $formId = null;
        $subject = $this->getSubject();
        if ($this->formId != null) {
            $formId = $this->formId;
        } elseif ($subject !== null && $subject->getForm() !== null) {
            /**
             * @var $subject ClientFormResponse
             */
            return $subject->getForm();
        } else {
            $formId = $this->getRequest()->get("form_id");
            if (!$formId) {
                throw new BadRequestHttpException("form_id must be set");
            }
        }
        /**
         * @var $currentForm ClientForm
         */
        $currentForm = $this->getModelManager()->find(ClientForm::class, $formId);
        if (!$currentForm) {
            throw new BadRequestHttpException("ClientForm $formId was not found");
        }
        return $currentForm;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ]);

        // из полей текущей формы составляем фейковые поля админки
        // значения этих полей будут читаться и сохраняться в объекте ClientFormResponse через магические методы
        $formFields = $this->getCurrentForm()->getFields();
        $subject = $this->getSubject();
        foreach ($formFields as $field) {
            /**
             * @var $field ClientFormField
             */
            $fieldName = "field_" . $field->getId();
            $fieldDesc = $this->getFieldDescription($field, $fieldName, $subject);
            $formMapper
                ->add($fieldName, $fieldDesc['type'], $fieldDesc['options']);
            if ($fieldDesc['type'] == CheckboxType::class) {
                // для полей-чекбоксов ещё навешиваем преобразователь типов, т.к. у всех полей значения строкового типа.
                $formMapper->getFormBuilder()->get($fieldName)
                    ->addViewTransformer(
                        new ClientFormCheckboxTransformer(
                            $subject !== null && $subject->getId() !== null ? $subject->getId() : null,
                            $field->getId()
                        ),
                        // важно указать здесь forcePrepend, иначе преобразователь BooleanType для поля-чекбокса
                        // симфонии ругнётся на строку
                        true
                    );
            }
        }
    }

    /**
     * По объекту поля формы определяет, какие параметры нужно передать в `FormMapper` админки.
     * Возвращает массив с полями `type` и `options` для метода `$formMapper->add()`
     *
     * @param ClientFormField $field
     * @param string $fieldName
     * @param ClientFormResponse $subject
     * @return array
     */
    private function getFieldDescription(ClientFormField $field, $fieldName, ClientFormResponse $subject)
    {
        $type = null;
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
                $choiceList = preg_split("/[\r\n]+/", $optionsText);
                // не включаем пустые строки в список для выбора
                $choiceList = array_filter($choiceList, function ($str) {
                    return trim($str) != '';
                });
                if ($choiceList === false) {
                    $choiceList = [];
                }
                $options['choices'] = array_combine($choiceList, $choiceList);
                // если в редактируемой анкете у этого поля выставлено значение, которого нет в списке для выбора,
                // пишем об этом в лог и добавляем значение в список
                if ($subject !== null && $subject->getId() !== null) {
                    $fieldValue = $subject->__get($fieldName);
                    $emptyAllowed = false;
                    if ($fieldValue === null && !$field->isRequired()) {
                        $emptyAllowed = true;
                    }
                    if (!array_key_exists($fieldValue, $options['choices']) && !$emptyAllowed) {
                        error_log("Missing choice " .
                            ($fieldValue === null ? 'null' : "'$fieldValue'") .
                            " in field $fieldName of client form response " . $subject->getId()
                        );
                        $options['choices'][$fieldValue] = $fieldValue;
                    }
                }
                break;
            case ClientFormField::TYPE_CHECKBOX:
                $type = CheckboxType::class;
                break;
            default:
                error_log("Unknown client form field type " . $field->getType());
                $type = TextType::class;
                break;
        }

        return [
            'type' => $type,
            'options' => $options,
        ];
    }

    public function prePersist($object)
    {
        $this->processFieldValues($object);
    }

    public function preUpdate($object)
    {
        $this->processFieldValues($object);
    }

    /**
     * @param ClientFormResponse $object
     */
    private function processFieldValues(ClientFormResponse $object)
    {
        $submittedFields = $object->_getSubmittedFields();
        // пока не умеем удалять поля формы
        $toRemove = [];
        $currentForm = $this->getCurrentForm();
        $object->setForm($currentForm);

        // обновляем значения уже привязанных к анкете полей
        // после этого в $submittedFields останутся только значения новых полей
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
            $fields = $this->getModelManager()->findBy(ClientFormField::class, [
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
                $fieldValue = new ClientFormResponseValue();
                $fieldValue->setClientFormResponse($object);
                $fieldValue->setClientFormField($fieldsById[$fieldId]);
                $fieldValue->setValue($value);

                $formValues[] = $fieldValue;
            }
            $object->setValues($formValues);
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ]);
    }
}
