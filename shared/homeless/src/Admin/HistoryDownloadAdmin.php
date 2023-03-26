<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\HistoryDownload;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.history_download.admin',
    'controller' => CRUDController::class,
    'label' => 'История скачиваний',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => HistoryDownload::class,
])]

class HistoryDownloadAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    ];

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
            ])
        ;
    }
}
