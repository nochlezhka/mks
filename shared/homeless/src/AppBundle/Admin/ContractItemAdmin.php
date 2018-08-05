<?php

namespace AppBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;

class ContractItemAdmin extends BaseAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', 'entity', [
                'label' => 'Тип',
                'required' => true,
                'class' => 'AppBundle\Entity\ContractItemType',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('dateStart', 'sonata_type_datetime_picker', [
                'view_timezone' => $this->getParameter('admin_view_timezone'),
                'label' => 'Дата начала',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => false,
                'attr' => [
                    'style' => 'width: 110px;'
                ],
            ])
            ->add('date', 'sonata_type_datetime_picker', [
                'view_timezone' => $this->getParameter('admin_view_timezone'),
                'label' => 'Дата выполнения',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => false,
                'attr' => [
                    'style' => 'width: 110px;'
                ],
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ]);
    }
}
