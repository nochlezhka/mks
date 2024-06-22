<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\UserController;
use App\Entity\Position;
use App\Entity\User;
use App\Form\DataTransformer\PositionToChoiceFieldMaskTypeTransformer;
use App\Security\User\Role;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\Form\Type\DatePickerType;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'sonata.user.admin.user',
    'controller' => UserController::class,
    'label' => 'users',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => User::class,
    'translation_domain' => 'SonataUserBundle',
])]
final class UserAdmin extends SonataUserAdmin
{
    use AdminTrait;

    public function __construct(
        private readonly PositionToChoiceFieldMaskTypeTransformer $transformer,
        #[Autowire(service: 'sonata.user.manager.user')]
        UserManagerInterface $manager,
    ) {
        parent::__construct($manager);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $isSuperAdmin = $user->hasRole(Role::SUPER_ADMIN);
        if (!$isSuperAdmin && $user !== $this->getSubject()) {
            throw new AccessDeniedException();
        }

        $positions = ['Другая должность' => ''];
        foreach ($this->entityManager->getRepository(Position::class)->findAll() as $item) {
            $positions[$item->getName()] = $item->getId();
        }

        $form->with('profile', ['class' => 'col-md-6']);
        $form
            ->add('lastname', null, [
                'required' => true,
            ])
            ->add('firstname', null, [
                'required' => true,
            ])
            ->add('middlename', null, [
                'required' => false,
                'label' => 'Отчество',
            ])
            ->add('position', ChoiceFieldMaskType::class, [
                'required' => false,
                'label' => 'Должность',
                'choices' => $positions,
                'multiple' => false,
                'map' => [
                    '' => ['positionText'],
                ],
            ])
            ->add('positionText', TextType::class, [
                'required' => false,
                'label' => 'Другая должность',
            ])
            ->add('proxyDate', DatePickerType::class, [
                'required' => false,
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата доверенности',
                'input' => 'datetime_immutable',
            ])
            ->add('proxyNum', null, [
                'required' => false,
                'label' => 'Номер доверенности',
            ])
            ->add('passport', TextareaType::class, [
                'required' => false,
                'label' => 'Паспортные данные',
            ])
        ;
        $form->end();

        $form->getFormBuilder()->get('position')->addModelTransformer($this->transformer);

        $form->with('general', ['class' => 'col-md-6']);
        $form
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, [
                'required' => $this->getSubject()?->getId() === null,
            ])
        ;

        if ($isSuperAdmin) {
            $form
                ->add('realRoles', RolesMatrixType::class, [
                    'label' => false,
                    'excluded_roles' => [
                        Role::EMPLOYEE,
                        Role::SONATA_ADMIN,
                        Role::ALLOWED_TO_SWITCH,
                    ],
                    'multiple' => true,
                    'required' => false,
                ])
            ;
        }

        $form
            ->add('enabled')
        ;

        $form->end();
    }
}
