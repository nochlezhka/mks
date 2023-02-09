<?php

namespace AppBundle\Admin;

use AppBundle\Entity\DocumentType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class DocumentAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'AppBundle';

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
                'required' => true,
                'class' => 'AppBundle\Entity\DocumentType',
                'group_by' => function ($val, $key, $index) {
                    if (($val instanceof DocumentType) && $val->getType() == DocumentType::TYPE_REGISTRATION) {
                        return 'Для постановки на учет';
                    }

                    return 'Прочие';
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.type', 'ASC')
                        ->addOrderBy('t.name', 'ASC');
                },
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
            ->add('date', 'sonata_type_date_picker', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда выдан',
                'required' => true,

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
            ->add('type.type', 'number', [
                'label' => 'Для постановки на учет',
                'template' => '/admin/fields/document_type_type_list.html.twig'
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
            ->add('date', null, [
                'label' => 'Когда выдан',
            ])
            ->add('createdAt', 'date', [
                'label' => 'Когда добавлен',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
