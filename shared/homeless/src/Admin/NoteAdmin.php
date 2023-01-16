<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Note;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'notes',
    'model_class' => Note::class,
    'controller' => CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class NoteAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'createdAt',
    );

    protected string $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
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
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('createdBy', null, [
                'label' => 'Кем добавлено',
            ])
            ->add('createdAt', 'date', [
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
                ]
            ]);
    }
}
