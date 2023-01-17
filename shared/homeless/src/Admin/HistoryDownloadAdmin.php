<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\HistoryDownload;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'История скачиваний',
    'model_class' => HistoryDownload::class,
    'controller' => CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class HistoryDownloadAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    );

    protected string $translationDomain = 'App';

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
