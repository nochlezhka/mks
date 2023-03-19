<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\ClientField;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'client_fields',
    'model_class' => ClientField::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
])]
class ClientFieldAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

    public function __construct(
        ClientFieldOptionAdmin $clientFieldOptionAdmin,
    ) {
        $this->addChild($clientFieldOptionAdmin, 'field');
        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
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
            ->add('type', ChoiceType::class, [
                'label' => 'Тип',
                'choices' => [
                    'Текст' => ClientField::TYPE_TEXT,
                    'Выбор варианта' => ClientField::TYPE_OPTION,
                    'Файл' => ClientField::TYPE_FILE,
                    'Дата/время' => ClientField::TYPE_DATETIME,
                ],
            ])
            ->add('multiple', null, [
                'label' => 'Допускается выбор нескольких вариантов одновременно (для типа "Выбор варианта")',
            ])
            ->add('description', null, [
                'label' => 'Описание',
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', 'number')
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Тип',
                'choices' => [
                    'Текст' => ClientField::TYPE_TEXT,
                    'Выбор варианта' => ClientField::TYPE_OPTION,
                    'Файл' => ClientField::TYPE_FILE,
                    'Дата/время' => ClientField::TYPE_DATETIME,
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
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureTabMenu(ItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && $action !== 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if ($admin->getSubject() instanceof ClientField && $admin->getSubject()->getType() === ClientField::TYPE_OPTION) {
            $menu->addChild(
                'Варианты выбора',
                ['uri' => $admin->generateUrl(ClientFieldOptionAdmin::class.'.list', ['id' => $id])],
            );
        }
    }
}
