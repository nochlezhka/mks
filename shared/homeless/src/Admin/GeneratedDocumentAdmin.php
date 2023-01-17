<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\GeneratedDocument;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Построить документ',
    'model_class' => GeneratedDocument::class,
    'controller'=> CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class GeneratedDocumentAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected string $translationDomain = 'App';

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => false,
                'class' => 'App\Entity\GeneratedDocumentType',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('number', null, [
                'label' => 'Номер',
                'required' => false,
            ])
            ->add('whom', null, [
                'label' => 'Кому',
                'required' => false,
            ])
            ->add('startText', 'entity', [
                'label' => 'Преамбула',
                'required' => false,
                'class' => 'App\Entity\GeneratedDocumentStartText',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add(TextType::class, null, [
                'label' => 'Основная часть',
                'required' => false,
            ])
            ->add('endText', EntityType::class, [
                'label' => 'Заключение',
                'required' => false,
                'class' => 'App\Entity\GeneratedDocumentEndText',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('signature', null, [
                'label' => 'Подпись',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
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
            ->add('createdAt', 'date', [
                'label' => 'Когда добавлен',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'download' => [
                        'template' => '/CRUD/list_generated_document_action_download.html.twig'
                    ],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
