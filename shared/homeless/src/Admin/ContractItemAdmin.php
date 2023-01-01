<?php

namespace App\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ContractItemAdmin extends BaseAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => true,
                'class' => 'App\Entity\ContractItemType',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('dateStart', DateTimePickerType::class, [
                'label' => 'Дата начала',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => false,
                'attr' => [
                    'style' => 'width: 110px;'
                ],
            ])
            ->add('date', DateTimePickerType::class, [
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