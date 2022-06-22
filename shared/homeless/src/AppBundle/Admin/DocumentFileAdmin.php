<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class DocumentFileAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('file', 'Vich\UploaderBundle\Form\Type\VichFileType', [
                'label' => 'Файл',
                'required' => true,
                'allow_delete' => false,
                'download_link' => true,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('file', null, [
                'label' => 'Файл',
                'template' => '/admin/fields/list_file.html.twig',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
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
