<?php

namespace App\Admin;

use App\Entity\Region;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Регионы',
    'model_class' => Region::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class RegionAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    );

    protected string $translationDomain = 'App';

    public function __construct(
        DistrictAdmin $districtAdmin
    )
    {
        $this->addChild($districtAdmin, 'region');
        parent::__construct();
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('shortName', null, [
                'label' => 'Сокращение',
                'required' => true,
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('shortName', null, [
                'label' => 'Сокращение',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    protected function configureTabMenu(ItemInterface $menu, string $action, AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && $action != 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            'Районы',
            ['uri' => $admin->generateUrl('app.district.admin.list', ['id' => $id])]
        );
    }
}
