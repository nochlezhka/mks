<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\ClientForm;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'client_forms',
    'model_class' => ClientForm::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
])]
class ClientFormAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

    public function __construct(ClientFormFieldAdmin $clientFormFieldAdmin)
    {
        $this->addChild($clientFormFieldAdmin, 'form');
        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('sort', TextType::class, [
                'label' => 'Сортировка',
                'required' => true,
            ])
        ;
    }

    /**
     * @throws \JsonException
     */
    protected function configureTabMenu(ItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['edit', 'show'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        if ($this->isGranted('EDIT')) {
            $menu
                ->addChild('Редактирование формы', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id]),
                ])
                ->addChild('Список полей', [
                    'uri' => $admin->generateUrl(ClientFormFieldAdmin::class.'.list', ['id' => $id]),
                ])
            ;
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'label' => 'Название',
        ]);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}
