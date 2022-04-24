<?php

namespace AppBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ShelterHistoryAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('status', 'entity', [
                'label' => 'Статус',
                'required' => true,
                'class' => 'AppBundle\Entity\ShelterStatus',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.sort', 'ASC');
                },
            ])
            ->add('contract', 'entity', [
                'label' => 'Договор',
                'required' => true,
                'class' => 'AppBundle\Entity\Contract',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.client =  :client')
                        ->orderBy('c.dateFrom', 'DESC')
                        ->setParameter('client', $this->getParent()->getSubject());
                },
            ])
            ->add('room', 'entity', [
                'label' => 'Комната',
                'required' => false,
                'class' => 'AppBundle\Entity\ShelterRoom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.number', 'ASC');
                },
            ])
            ->add('dateFrom', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата заселения',
                'required' => false,
            ])
            ->add('dateTo', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата выселения',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->add('fluorographyDate', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата флюорографии',
                'required' => false,
            ])
            ->add('diphtheriaVaccinationDate', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от дифтерии',
                'required' => false,
            ])
            ->add('hepatitisVaccinationDate', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от гепатита',
                'required' => false,
            ])
            ->add('typhusVaccinationDate', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата прививки от тифа',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
