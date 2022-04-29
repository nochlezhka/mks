<?php

namespace Application\Sonata\UserBundle\Admin;

use AppBundle\Admin\BaseAdminTrait;
use AppBundle\Entity\Position;
use Application\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserAdmin extends BaseUserAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $isSuperAdmin = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');

        if (!$user instanceof User || (!$isSuperAdmin && $user != $this->getSubject())) {
            throw new AccessDeniedException();
        }

        if ($isSuperAdmin) {
            $formMapper
                ->tab('User');
        }

        $formMapper
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end();

        if ($isSuperAdmin) {
            $formMapper
                ->end();
        }

        if ($isSuperAdmin) {
            $formMapper
                ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->end();
        }

        if ($isSuperAdmin) {
            $formMapper
                ->tab('User');
        }
        $positions = [
            '' => 'Другая должность'
        ];
        foreach ($this->getConfigurationPool()->getContainer()->get('app.position_option.repository')->findAll() as $item) {
            $positions[$item->getId()] = $item->getName();
        }

        $formMapper
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
                'label' => 'Дата доверенности',
            ))
            ->add('proxyNum', null, array('required' => false, 'label' => 'Номер доверенности'))
            ->add('passport', TextareaType::class, array('required' => false, 'label' => 'Паспортные данные'))
            ->end();

        $transformer = $this
            ->getConfigurationPool()
            ->getContainer()->get('app.position_to_choice_field_mask_type.transformer');
        $formMapper->getFormBuilder()->get('position')->addModelTransformer($transformer);
        if ($isSuperAdmin) {
            $formMapper
                ->end();
        }

        if ($isSuperAdmin) {
            $formMapper
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
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('id', null, ['advanced_filter' => false])
            ->add('username', null, ['advanced_filter' => false])
            ->add('locked', null, ['advanced_filter' => false])
            ->add('email', null, ['advanced_filter' => false])
            ->add('groups', null, ['advanced_filter' => false]);
    }
}
