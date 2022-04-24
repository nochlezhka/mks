<?php


namespace AppBundle\Admin;

use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponse;
use AppBundle\Form\DataTransformer\ClientFormCheckboxTransformer;
use AppBundle\Form\DataTransformer\ClientFormMultiselectTransformer;
use AppBundle\Repository\ClientFormResponseRepository;
use AppBundle\Util\BaseEntityUtil;
use AppBundle\Util\ClientFormUtil;
use Doctrine\ORM\EntityManager;
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
        // из полей текущей формы составляем фейковые поля админки
        // значения этих полей будут читаться и сохраняться в объекте ClientFormResponse через магические методы
        $formFields = $this->getCurrentForm()->getFields()->toArray();
        /**
         * @var $formFields ClientFormField[]
         */
        BaseEntityUtil::sortEntities($formFields);
        $subject = $this->getSubject();
        foreach ($formFields as $field) {
            $fieldName = "field_" . $field->getId();
            $fieldDesc = $this->getFieldDescription($field, $fieldName, $subject);
            $formMapper
                ->add($fieldName, $fieldDesc['type'], $fieldDesc['options']);
            if ($fieldDesc['type'] == CheckboxType::class) {
                // для полей-чекбоксов ещё навешиваем преобразователь типов, т.к. у всех полей значения строкового типа.
                $formMapper->getFormBuilder()->get($fieldName)
                    ->addModelTransformer(
                        new ClientFormCheckboxTransformer(
                            $subject !== null && $subject->getId() !== null ? $subject->getId() : null,
                            $field->getId()
                        )
                    );
            } elseif ($fieldDesc['type'] == ChoiceType::class && isset($fieldDesc['options']['multiple'])
                && $fieldDesc['options']['multiple']
            ) {
                // для селектов со множественным выбором навешиваем преобразователь типов из строк в массив строк
                // и обратно
                $formMapper->getFormBuilder()->get($fieldName)
                    ->addModelTransformer(
                        new ClientFormMultiselectTransformer(
                            $subject !== null && $subject->getId() !== null ? $subject->getId() : null,
                            $field->getId()
                        )
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
                $choiceList = ClientFormUtil::optionsTextToArray($optionsText);
                $options['choices'] = array_combine($choiceList, $choiceList);
                // если в редактируемой анкете у этого поля выставлено значение, которого нет в списке для выбора,
                // пишем об этом в лог и добавляем значение в список
                if ($subject !== null && $subject->getId() !== null) {
                    $value = $subject->__get($fieldName);
                    $fieldValues = [];
                    if ($value !== null) {
                        if ($field->isMultiselect()) {
                            $fieldValues = ClientFormUtil::optionsTextToArray($value);
                        } else {
                            $fieldValues = [$value];
                        }
                    }
                    foreach ($fieldValues as $fieldValue) {
                        if (!array_key_exists($fieldValue, $options['choices'])) {
                            error_log("Missing choice " . ($fieldValue === null ? 'null' : "'$fieldValue'") .
                                " in field $fieldName of client form response " . $subject->getId()
                            );
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
                error_log("Unknown client form field type " . $field->getType());
                $type = TextType::class;
                break;
        }

        return [
            'type' => $type,
            'options' => $options,
        ];
    }

    /**
     * Создание анкеты. Оборачиваем в транзакцию, т.к. нужно консистентно писать в несколько таблиц.
     *
     * @param mixed $object
     * @return bool|mixed
     * @throws \Exception
     */
    public function create($object)
    {
        return $this->getEntityManager()->transactional(function (EntityManager $em) use ($object) {
            $this->getClientFormResponseRepository()->prepareForCreateOrUpdate($object, $this->getCurrentForm());
            return parent::create($object);
        });
    }

    /**
     * Обновление анкеты. Оборачиваем в транзакцию для консистентности таблицы `_value`
     * и лочимся об запись в `client_form_response`, чтобы случайно не смешать несколько параллельных обновлений.
     *
     * @param mixed $object
     * @return bool|mixed
     * @throws \Exception
     */
    public function update($object)
    {
        return $this->getEntityManager()->transactional(function (EntityManager $em) use ($object) {
            $this->getClientFormResponseRepository()->lockForUpdate($object);
            $this->getClientFormResponseRepository()->prepareForCreateOrUpdate($object, $this->getCurrentForm());
            return parent::update($object);
        });
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return ClientFormResponseRepository
     */
    private function getClientFormResponseRepository()
    {
        return $this->getEntityManager()->getRepository(ClientFormResponse::class);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $firstField = $this->getCurrentForm()->getFirstField();
        if ($firstField === null) {
            return;
        }
        $listMapper
            ->addIdentifier('firstFieldValue', null, [
                'label' => $firstField->getName(),
            ])->add('isFull', 'boolean', [
                'label' => 'Заполнено',
            ]);
        $listMapper
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
