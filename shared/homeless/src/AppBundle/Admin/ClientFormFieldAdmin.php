<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientFormField;
use AppBundle\Util\ClientFormUtil;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Админка для редактирования поля формы
 *
 * @package AppBundle\Admin
 */
class ClientFormFieldAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $parentAssociationMapping = 'form';

    protected $translationDomain = 'AppBundle';

    /**
     * @inheritDoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('type', ChoiceFieldMaskType::class, [
                'label' => 'Тип поля',
                'choices' => [
                    ClientFormField::TYPE_TEXT => 'Текст',
                    ClientFormField::TYPE_OPTION => 'Выбор варианта',
                    ClientFormField::TYPE_CHECKBOX => 'Чекбокс',
                ],
                'map' => [
                    ClientFormField::TYPE_TEXT => [],
                    ClientFormField::TYPE_OPTION => ['options', 'multiselect'],
                    ClientFormField::TYPE_CHECKBOX => [],
                ],
            ]);
        $optionsAttrs = [];
        if ($this->getSubject() !== null) {
            $optionsText = $this->getSubject()->getOptions();
            $choiceList = ClientFormUtil::optionsTextToArray($optionsText);
            $optionsAttrs['rows'] = count($choiceList);
        }
        $formMapper
            ->add('options', TextareaType::class, [
                'label' => 'Варианты',
                'help' => 'Каждый вариант в своей строке. ' .
                    '<br>Удаление или изменение варианта не приведёт к изменению полей в уже заполненных анкетах!',
                'required' => false,
                'attr' => $optionsAttrs,
            ])
            ->add('multiselect', CheckboxType::class, [
                'label' => 'Множественный выбор',
                'required' => false,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'Обязательное',
                'required' => false,
            ])
            ->add('sort', 'text', [
                'label' => 'Сортировка',
                'required' => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('name', null, ['label' => 'Название'])
            ->add('type', 'choice', [
                'label' => 'Тип поля',
                'choices' => [
                    ClientFormField::TYPE_TEXT => 'Текст',
                    ClientFormField::TYPE_OPTION => 'Выбор варианта',
                    ClientFormField::TYPE_CHECKBOX => 'Чекбокс',
                ],
            ])
            ->add('options', null, [
                'label' => 'Варианты',
            ])
            ->add('required', 'boolean', [
                'label' => 'Обязательное',
            ])
            ->add('multiselect', 'boolean', [
                'label' => 'Множественный выбор',
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('sort', 'text', [
                'label' => 'Сортировка',
            ]);

        $listMapper->add('_action', null, [
            'label' => 'Действие',
            'actions' => ['show' => [], 'edit' => []],
        ]);
    }

    /**
     * @inheritDoc
     * @param ClientFormField $object
     */
    public function hasAccess($action, $object = null)
    {
        // на всякий случай запрещаем массовое удаление, т.к. среди полей формы могут быть те, которые нельзя удалять
        if ($action == 'batchDelete') {
            return false;
        }
        if (($action == 'delete' || $action == 'edit') && $object !== null) {
            if ($object->isFixed()) {
                return false;
            }
        }
        return parent::hasAccess($action, $object);
    }

    /**
     * @inheritDoc
     * @param ClientFormField $object
     */
    public function checkAccess($action, $object = null)
    {
        // на всякий случай запрещаем массовое удаление, т.к. среди полей формы могут быть те, которые нельзя удалять
        if ($action == 'batchDelete') {
            throw new AccessDeniedException("Массовое удаление полей формы запрещено.");
        }
        // здесь нет запрета на редактирование фиксированного поля, т.к. она унесена в валидацию формы
        // таким образом остаётся возможность открыть страницу редактирования поля, но сохранить его нельзя.
        if ($action == 'delete' && $object !== null) {
            if ($object->isFixed()) {
                throw new AccessDeniedException(sprintf(
                    "Удаление поля %s запрещено.", $object->getName()
                ));
            }
        }
        parent::checkAccess($action, $object);
    }

    /**
     * @inheritDoc
     * @param ClientFormField $object
     */
    public function preValidate($object)
    {
        if ($object->isFixed()) {
            throw new AccessDeniedException(sprintf(
                "Изменение поля %s запрещено.", $object->getName()
            ));
        }
        parent::preValidate($object);
    }
}
