<?php

namespace App\Admin;

use App\Entity\Position;
use App\Entity\User;
use App\Form\DataTransformer\PositionToChoiceFieldMaskTypeTransformer;
use JsonException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\DatePickerType;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'model_class' => User::class,
])]
class UserAdmin extends BaseUserAdmin
{
    use BaseAdminTrait;

    private AuthorizationCheckerInterface $authorizationChecker;

    private PositionToChoiceFieldMaskTypeTransformer $transformer;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        PositionToChoiceFieldMaskTypeTransformer $transformer,
        #[Autowire(service: "sonata.user.manager.user")]
        UserManagerInterface $manager
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->transformer = $transformer;
        parent::__construct($manager);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $isSuperAdmin = $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        if (!$user instanceof User || (!$isSuperAdmin && $user != $this->getSubject())) {
            throw new AccessDeniedException();
        }

        if ($isSuperAdmin) {
            $form
                ->tab('User');
        }

        $form
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end();

        if ($isSuperAdmin) {
            $form
                ->end();
        }

        if ($isSuperAdmin) {
            $form
                ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->end();
        }

        if ($isSuperAdmin) {
            $form
                ->tab('User');
        }
        $positions = [
            'Другая должность' => ''
        ];
        foreach ($this->manager->getRepository(Position::class)->findAll() as $item) {
            $positions[$item->getName()] = $item->getId();
        }

        $form
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ))
            ->end()
            ->with('Profile')
            ->add('lastname', null, array('required' => false))
            ->add('firstname', null, array('required' => false))
            ->add('middlename', null, array('required' => false, 'label' => 'Отчество'))
            ->add('position', ChoiceFieldMaskType::class, array(
                'required' => false,
                'label' => 'Должность',
                'choices' => $positions,
                'multiple' => false,
                'map' => [
                    '' => ['positionText']
                ],
            ))
            ->add('positionText', TextType::class, [
                'required' => false,
                'label' => 'Другая должность',
            ])
            ->add('proxyDate', DatePickerType::class, array(
                'required' => false,
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата доверенности',
            ))
            ->add('proxyNum', null, array('required' => false, 'label' => 'Номер доверенности'))
            ->add('passport', TextareaType::class, array('required' => false, 'label' => 'Паспортные данные'))
            ->end();

        $form->getFormBuilder()->get('position')->addModelTransformer($this->transformer);
        if ($isSuperAdmin) {
            $form
                ->end();
        }

        if ($isSuperAdmin) {
            $form
                ->tab('Security')
                ->with('Status')
                ->add('enabled', null, array('required' => false))
                ->end()
                ->with('Groups')
                ->add('groups', ModelType::class, array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['advanced_filter' => false])
            ->add('username', null, ['advanced_filter' => false])
            ->add('email', null, ['advanced_filter' => false])
            ->add('groups', null, ['advanced_filter' => false]);
    }

    /**
     * Переопределяем метод, чтобы использовать кастомный impersonating.html.twig, в котором есть дополнительные
     * ограничения на то, можно перевоплощаться в данного пользователя или нет
     *
     * @throws JsonException
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $list
                ->add('impersonating', 'string', ['template' => '/admin/fields/impersonating.html.twig'])
            ;
        }
    }
}
