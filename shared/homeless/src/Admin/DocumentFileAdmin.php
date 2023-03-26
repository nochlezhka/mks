<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\DocumentFile;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Vich\UploaderBundle\Form\Type\VichFileType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.document_file.admin',
    'label' => 'document_files',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => DocumentFile::class,
])]
class DocumentFileAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('file', VichFileType::class, [
                'label' => 'Файл',
                'required' => true,
                'allow_delete' => false,
                'download_uri' => true,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('file', null, [
                'label' => 'Файл',
                'template' => '/admin/fields/list_file.html.twig',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлен',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
                'admin_code' => UserAdmin::class,
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
