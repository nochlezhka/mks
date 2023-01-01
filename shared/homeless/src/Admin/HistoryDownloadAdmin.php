<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;

class HistoryDownloadAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    );

    protected $translationDomain = 'App';

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('user', null, [
                'label' => 'Кем скачана',
            ])
            ->add('certificateType.name', null, [
                'label' => 'Тип',
            ])
            ->add('date', 'date', [
                'label' => 'Создано',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('delete', null, [
                'label' => 'Удалить',
                'template' => '/CRUD/list_delete.html.twig',
            ]);
    }
}
