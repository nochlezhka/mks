<?php

namespace App\Admin;

use App\Entity\ContractItem;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Пункты сервисного плана',
    'model_class' => ContractItem::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
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
