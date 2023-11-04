<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ClientFormField;
use App\Util\ClientFormUtil;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Админка для редактирования поля формы
 */
#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.client_form_field.admin',
    'label' => 'ClientFormField',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ClientFormField::class,
])]
class ClientFormFieldAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

    public function preValidate(object $object): void
    {
        if (!$object instanceof ClientFormField) {
            return;
        }

        if ($object->isFixed()) {
            throw new AccessDeniedException(sprintf(
                'Изменение поля %s запрещено.', $object->getName(),
            ));
        }
        parent::preValidate($object);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('type', ChoiceFieldMaskType::class, [
                'label' => 'Тип поля',
                'choices' => [
                    'Текст' => ClientFormField::TYPE_TEXT,
                    'Выбор варианта' => ClientFormField::TYPE_OPTION,
                    'Чекбокс' => ClientFormField::TYPE_CHECKBOX,
                ],
                'map' => [
                    ClientFormField::TYPE_TEXT => [],
                    ClientFormField::TYPE_OPTION => ['options', 'multiselect'],
                    ClientFormField::TYPE_CHECKBOX => [],
                ],
            ])
        ;
        $optionsAttrs = [];
        if ($this->getSubject() !== null) {
            $optionsText = $this->getSubject()->getOptions();
            $choiceList = ClientFormUtil::optionsTextToArray($optionsText);
            $optionsAttrs['rows'] = \count($choiceList);
        }
        $form
            ->add('options', TextareaType::class, [
                'label' => 'Варианты',
                'help' => 'Каждый вариант в своей строке. <br>Удаление или изменение варианта не приведёт к изменению полей в уже заполненных анкетах!',
                'help_html' => true,
                'required' => false,
                'attr' => $optionsAttrs,
            ])
            ->add('multiselect', CheckboxType::class, [
                'label' => 'Множественный выбор',
                'required' => false,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'Обязательное',
                'required' => false,
            ])
            ->add('sort', TextType::class, [
                'label' => 'Сортировка',
                'required' => true,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name', null, [
                'label' => 'Название',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Тип поля',
                'choices' => [
                    'Текст' => ClientFormField::TYPE_TEXT,
                    'Выбор варианта' => ClientFormField::TYPE_OPTION,
                    'Чекбокс' => ClientFormField::TYPE_CHECKBOX,
                ],
            ])
            ->add('options', null, [
                'label' => 'Варианты',
            ])
            ->add('required', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Обязательное',
            ])
            ->add('multiselect', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Множественный выбор',
            ])
            ->add('sort', null, [
                'label' => 'Сортировка',
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('sort', FieldDescriptionInterface::TYPE_STRING, [
                'label' => 'Сортировка',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => ['show' => [], 'edit' => []],
            ])
        ;
    }
}
