<?php

namespace App\Admin;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ShelterHistoryAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected string $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('status', EntityType::class, [
                'label' => 'Статус',
                'required' => true,
                'class' => 'App\Entity\ShelterStatus',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.sort', 'ASC');
                },
            ])
            ->add('contract', EntityType::class, [
                'label' => 'Договор',
                'required' => true,
                'class' => 'App\Entity\Contract',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.client =  :client')
                        ->orderBy('c.dateFrom', 'DESC')
                        ->setParameter('client', $this->getParent()->getSubject());
                },
            ])
            ->add('room', EntityType::class, [
                'label' => 'Комната',
                'required' => false,
                'class' => 'App\Entity\ShelterRoom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.number', 'ASC');
                },
            ])
            ->add('dateFrom', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата заселения',
                'required' => false,
            ])
            ->add('dateTo', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата выселения',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->add('fluorographyDate', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата флюорографии',
                'required' => false,
            ])
            ->add('diphtheriaVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от дифтерии',
                'required' => false,
            ])
            ->add('hepatitisVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от гепатита',
                'required' => false,
            ])
            ->add('typhusVaccinationDate', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от тифа',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
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
            ->add('fluorographyDate', 'date', [
                'label' => 'Дата флюорографии',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('diphtheriaVaccinationDate', 'date', [
                'label' => 'Дата прививки от дифтерии',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('hepatitisVaccinationDate', 'date', [
                'label' => 'Дата прививки от гепатита',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('typhusVaccinationDate', 'date', [
                'label' => 'Дата прививки от тифа',
                'pattern' => 'dd.MM.YYYY',
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
