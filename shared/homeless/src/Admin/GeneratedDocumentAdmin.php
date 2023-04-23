<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\GeneratedDocument;
use App\Entity\GeneratedDocumentEndText;
use App\Entity\GeneratedDocumentStartText;
use App\Entity\GeneratedDocumentType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.generated_document.admin',
    'controller' => CRUDController::class,
    'label' => 'Построить документ',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => GeneratedDocument::class,
])]
class GeneratedDocumentAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('download', $this->getRouterIdParameter().'/download');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => false,
                'class' => GeneratedDocumentType::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC'),
            ])
            ->add('number', null, [
                'label' => 'Номер',
                'required' => false,
            ])
            ->add('whom', null, [
                'label' => 'Кому',
                'required' => false,
            ])
            ->add('startText', EntityType::class, [
                'label' => 'Преамбула',
                'required' => false,
                'class' => GeneratedDocumentStartText::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC'),
            ])
            ->add('text', null, [
                'label' => 'Основная часть',
                'required' => false,
            ])
            ->add('endText', EntityType::class, [
                'label' => 'Заключение',
                'required' => false,
                'class' => GeneratedDocumentEndText::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC'),
            ])
            ->add('signature', null, [
                'label' => 'Подпись',
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('type', null, [
                'label' => 'Тип',
            ])
            ->add('number', null, [
                'label' => 'Номер',
            ])
            ->add('whom', null, [
                'label' => 'Кому',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлен',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
                'admin_code' => 'sonata.user.admin.user',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'download' => [
                        'template' => '/CRUD/list_generated_document_action_download.html.twig',
                    ],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }
}
