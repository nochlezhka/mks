<?php

namespace AppBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GeneratedDocumentAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'AppBundle';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    public function configure()
    {
        $this->parentAssociationMapping = 'client';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', 'entity', [
                'label' => 'Тип',
                'required' => false,
                'class' => 'AppBundle\Entity\GeneratedDocumentType',
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
                'class' => 'AppBundle\Entity\GeneratedDocumentStartText',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('text', null, [
                'label' => 'Основная часть',
                'required' => false,
            ])
            ->add('endText', 'entity', [
                'label' => 'Заключение',
                'required' => false,
                'class' => 'AppBundle\Entity\GeneratedDocumentEndText',
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
            ->add('_action', null, [
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
