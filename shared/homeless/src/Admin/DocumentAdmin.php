<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Document;
use App\Entity\DocumentType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.document.admin',
    'label' => 'documents',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Document::class,
])]

class DocumentAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => true,
                'class' => DocumentType::class,
                'group_by' => static fn ($val): string => ($val instanceof DocumentType) && $val->getType() === DocumentType::TYPE_REGISTRATION ? 'Для постановки на учет' : 'Прочие',
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                    ->orderBy('t.type', 'ASC')
                    ->addOrderBy('t.name', 'ASC'),
            ])
            ->add('numberPrefix', null, [
                'label' => 'Серия',
            ])
            ->add('number', null, [
                'label' => 'Номер',
            ])
            ->add('issued', null, [
                'label' => 'Кем выдан',
            ])
            ->add('date', DatePickerType::class, [
                'datepicker_options' => [
                    'defaultDate' => (new \DateTimeImmutable())->format('Y-m-d'),
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда выдан',
                'required' => true,
                'input' => 'datetime_immutable',
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('type', null, [
                'label' => 'Тип',
            ])
            ->add('type.type', 'number', [
                'label' => 'Для постановки на учет',
                'template' => '/admin/fields/document_type_type_list.html.twig',
            ])
            ->add('numberPrefix', null, [
                'label' => 'Серия',
            ])
            ->add('number', null, [
                'label' => 'Номер',
            ])
            ->add('issued', null, [
                'label' => 'Кем выдан',
            ])
            ->add('date', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда выдан',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлен',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
                'admin_code' => 'sonata.user.admin.user',
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
