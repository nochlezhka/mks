<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ContractItem;
use App\Entity\ContractItemType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.contract_item.admin',
    'label' => 'Пункты сервисного плана',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ContractItem::class,
])]
class ContractItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => true,
                'class' => ContractItemType::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC'),
            ])
            ->add('dateStart', DateTimePickerType::class, [
                'label' => 'Дата начала',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => false,
                'attr' => [
                    'style' => 'width: 110px;',
                ],
            ])
            ->add('date', DateTimePickerType::class, [
                'label' => 'Дата выполнения',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => false,
                'attr' => [
                    'style' => 'width: 110px;',
                ],
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
        ;
    }
}
