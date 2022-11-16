<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CertificateTypeAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('downloadable', null, [
                'label' => 'Справка доступна для скачивания',
                'required' => false,
            ])
            ->add('showPhoto', null, [
                'label' => 'Отображать фото клиента',
                'required' => false,
            ])
            ->add('contentHeaderLeft', null, [
                'label' => 'Содержимое верхнего левого блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentHeaderRight', null, [
                'label' => 'Содержимое верхнего правого блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentBodyRight', null, [
                'label' => 'Содержимое среднего блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('contentFooter', null, [
                'label' => 'Содержимое нижнего блока',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
                'required' => true,
                'attr' => ['rows' => 5],
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('downloadable', null, [
                'label' => 'Доступна для скачивания',
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
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
