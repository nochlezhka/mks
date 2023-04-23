<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\CertificateType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.certificate_type.admin',
    'label' => 'certificate_types',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => CertificateType::class,
])]

class CertificateTypeAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

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
            ])
        ;
    }

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
                ],
            ])
        ;
    }
}
