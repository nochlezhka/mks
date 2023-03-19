<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\Contract;
use App\Entity\ShelterHistory;
use App\Entity\ShelterRoom;
use App\Entity\ShelterStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'shelter_history',
    'model_class' => ShelterHistory::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
])]
class ShelterHistoryAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('status', EntityType::class, [
                'label' => 'Статус',
                'required' => true,
                'class' => ShelterStatus::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('s')
                    ->orderBy('s.sort', 'ASC'),
            ])
            ->add('contract', EntityType::class, [
                'label' => 'Договор',
                'required' => true,
                'class' => Contract::class,
                'query_builder' => fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('c')
                    ->where('c.client =  :client')
                    ->orderBy('c.dateFrom', 'DESC')
                    ->setParameter('client', $this->getParent()->getSubject()),
            ])
            ->add('room', EntityType::class, [
                'label' => 'Комната',
                'required' => false,
                'class' => ShelterRoom::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('r')
                    ->orderBy('r.number', 'ASC'),
            ])
            ->add('dateFrom', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата заселения',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('dateTo', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата выселения',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->add('fluorographyDate', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата флюорографии',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('diphtheriaVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от дифтерии',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('hepatitisVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от гепатита',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('typhusVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от тифа',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('status', null, [
                'label' => 'Статус',
            ])
            ->add('room', null, [
                'label' => 'Комната',
            ])
            ->add('dateFrom', 'date', [
                'label' => 'Дата заселения',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('dateTo', 'date', [
                'label' => 'Дата выселения',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->add('fluorographyDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата флюорографии',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('diphtheriaVaccinationDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата прививки от дифтерии',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('hepatitisVaccinationDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата прививки от гепатита',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('typhusVaccinationDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата прививки от тифа',
                'pattern' => 'dd.MM.YYYY',
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
