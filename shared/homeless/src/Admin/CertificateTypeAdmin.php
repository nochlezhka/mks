<?php

namespace App\Admin;

use App\Controller\ClientController;
use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Entity\Client;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Типы справок',
    'model_class' => CertificateType::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class CertificateTypeAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected string $translationDomain = 'App';

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
            ->add('downloadable', null, [
                'label' => 'Справка доступна для скачивания',
                'required' => false,
            ])
            ->add('showPhoto', null, [
                'label' => 'Отображать фото клиента',
                'required' => false,
            ])
            ->add('contentHeaderLeft', null, [
                'label' => 'Содержимое верхнего левого блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentHeaderRight', null, [
                'label' => 'Содержимое верхнего правого блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentBodyRight', null, [
                'label' => 'Содержимое среднего блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentFooter', null, [
                'label' => 'Содержимое нижнего блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
                'required' => true,
                'attr' => ['rows' => 5],
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
            ->add('downloadable', null, [
                'label' => 'Доступна для скачивания',
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
