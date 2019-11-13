<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientFormField;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClientFormFieldAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $parentAssociationMapping = 'form';

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Тип поля',
                'choices' => [
                    ClientFormField::TYPE_TEXT => 'Текст',
                    ClientFormField::TYPE_OPTION => 'Выбор варианта',
                    ClientFormField::TYPE_CHECKBOX => 'Чекбокс',
                ],
            ])
            ->add('options', TextareaType::class, [
                'label' => 'Варианты',
                'help' => 'Каждый вариант в своей строке. '.
                    '<br>Удаление или изменение варианта не приведёт к изменению полей в уже заполненных анкетах!',
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
     * @param ListMapper $listMapper
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
    }
}
