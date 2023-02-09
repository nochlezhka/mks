<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ClientField;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientFieldAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $translationDomain = 'AppBundle';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, [
                'label' => 'Включено для всех',
            ])
            ->add('enabledForHomeless', null, [
                'label' => 'Включено для бездомных',
            ])
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('code', null, [
                'label' => 'Код',
                'required' => true,
            ])
            ->add('sort', null, [
                'label' => 'Порядок сортировки',
                'required' => true,
            ])
            ->add('required', null, [
                'label' => 'Обязательное для всех',
            ])
            ->add('mandatoryForHomeless', null, [
                'label' => 'Обязательное для бездомных',
            ])
            ->add('type', 'choice', [
                'label' => 'Тип',
                'choices' => [
                    ClientField::TYPE_TEXT => 'Текст',
                    ClientField::TYPE_OPTION => 'Выбор варианта',
                    ClientField::TYPE_FILE => 'Файл',
                    ClientField::TYPE_DATETIME => 'Дата/время',
                ],
            ])
            ->add('multiple', null, [
                'label' => 'Допускается выбор нескольких вариантов одновременно (для типа "Выбор варианта")',
            ])
            ->add('description', null, [
                'label' => 'Описание',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'number')
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('type', 'choice', [
                'label' => 'Тип',
                'choices' => [
                    ClientField::TYPE_TEXT => 'Текст',
                    ClientField::TYPE_OPTION => 'Выбор варианта',
                    ClientField::TYPE_FILE => 'Файл',
                    ClientField::TYPE_DATETIME => 'Дата/время',
                ],
            ])
            ->add('sort', 'number', [
                'label' => 'Порядок сортировки',
            ])
            ->add('required', null, [
                'label' => 'Обязательное для всех',
            ])
            ->add('mandatoryForHomeless', null, [
                'label' => 'Обязательное для бездомных',
            ])
            ->add('enabled', null, [
                'label' => 'Включено для всех',
            ])
            ->add('enabledForHomeless', null, [
                'label' => 'Включено для бездомных',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param MenuItemInterface $menu
     * @param string $action
     * @param AdminInterface|null $childAdmin
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if ($admin->getSubject() instanceof ClientField && $admin->getSubject()->getType() == ClientField::TYPE_OPTION) {
            $menu->addChild(
                'Варианты выбора',
                ['uri' => $admin->generateUrl('app.client_field_option.admin.list', ['id' => $id])]
            );
        }
    }
}
