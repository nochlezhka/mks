<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

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
    'manager_type' => 'orm',
    'label' => 'users',
    'model_class' => User::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'translation_domain' => 'SonataUserBundle',
])]
class UserAdmin extends SonataUserAdmin
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

        if ($isSuperAdmin) {
            $form->tab('user');
        }

        $form
            ->with('profile', ['class' => 'col-md-6'])->end()
            ->with('general', ['class' => 'col-md-6'])->end()
        ;

        if ($isSuperAdmin) {
            $form->end();
        }

        if ($isSuperAdmin) {
            $form->tab('roles');
            $form
                ->with('roles', ['class' => 'col-md-12'])->end()
            ;
            $form->end();
        }

        if ($isSuperAdmin) {
            $form->tab('user');
        }

        $positions = ['Другая должность' => ''];
        foreach ($this->entityManager->getRepository(Position::class)->findAll() as $item) {
            $positions[$item->getName()] = $item->getId();
        }

        $form->with('profile');
        $form
            ->add('lastname', null, [
                'required' => false,
            ])
            ->add('firstname', null, [
                'required' => false,
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

        $form->with('general');
        $form
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, [
                'required' => $this->getSubject()?->getId() === null,
            ])
            ->add('enabled')
        ;
        $form->end();

        if ($isSuperAdmin) {
            $form->end();
        }

        if (!$isSuperAdmin) {
            return;
        }

        $form->tab('roles');

        $form->with('roles');
        $form
            ->add('realRoles', RolesMatrixType::class, [
                'label' => false,
                'multiple' => true,
                'required' => false,
            ])
        ;
        $form->end();

        $form->end();
    }
}
