<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Note;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.note.admin',
    'controller' => CRUDController::class,
    'label' => 'notes',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Note::class,
])]
final class NoteAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('text', SimpleFormatterType::class, [
                'label' => 'Текст',
                'required' => true,
                'format' => 'richhtml',
                'ckeditor_context' => 'homeless',
            ])
            ->add('important', null, [
                'label' => 'Важное',
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('createdBy', null, [
                'label' => 'Кем добавлено',
                'admin_code' => 'sonata.user.admin.user',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлено',
            ])
            ->add('text', null, [
                'label' => 'Текст',
                'template' => '/admin/fields/note_text_list.html.twig',
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
