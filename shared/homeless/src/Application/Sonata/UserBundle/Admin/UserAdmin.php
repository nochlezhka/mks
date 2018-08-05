<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin;

use AppBundle\Admin\BaseAdminTrait;
use AppBundle\Entity\Position;
use Application\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class UserAdmin extends BaseUserAdmin
{

    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $isSuperAdmin = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');

        if (!$user instanceof User || (!$isSuperAdmin && $user != $this->getSubject())) {
            throw new AccessDeniedException();
        }

        //parent::configureFormFields($formMapper);

        // define group zoning
        if ($isSuperAdmin) {
            $formMapper
                ->tab('User');
        }

        $formMapper
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end();
        //->with('Social', array('class' => 'col-md-6'))->end()

        if ($isSuperAdmin) {
            $formMapper
                ->end();
        }

        if ($isSuperAdmin) {
            $formMapper
                ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                //->with('Keys', array('class' => 'col-md-4'))->end()
                //->with('Roles', array('class' => 'col-md-12'))->end()
                ->end();
        }

        $now = new \DateTime();

        if ($isSuperAdmin) {
            $formMapper
                ->tab('User');
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
            /*
            ->add('dateOfBirth', DatePickerType::class, array(
                'years' => range(1900, $now->format('Y')),
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ))
            */
            ->add('lastname', null, array('required' => false))
            ->add('firstname', null, array('required' => false))
            ->add('middlename', null, array('required' => false, 'label' => 'Отчество'))
            ->add('position', EntityType::class, array(
                'required' => false,
                'label' => 'Должность',
                'class' => Position::class,
                'property' => 'name',
                'multiple' => false,
            ))
            ->add('proxyDate', DatePickerType::class, array(
                'required' => false,
                'label' => 'Дата доверенности',
            ))
            ->add('proxyNum', null, array('required' => false, 'label' => 'Номер доверенности'))
            ->add('passport', TextareaType::class, array('required' => false, 'label' => 'Паспортные данные'))
            /*
            ->add('website', UrlType::class, array('required' => false))
            ->add('biography', TextType::class, array('required' => false))
            ->add('gender', UserGenderListType::class, array(
                'required' => true,
                'translation_domain' => $this->getTranslationDomain(),
            ))
            ->add('locale', LocaleType::class, array('required' => false))
            ->add('timezone', TimezoneType::class, array('required' => false))
            ->add('phone', null, array('required' => false))
            */
            ->end();
        /*
        ->with('Social')
        ->add('facebookUid', null, array('required' => false))
        ->add('facebookName', null, array('required' => false))
        ->add('twitterUid', null, array('required' => false))
        ->add('twitterName', null, array('required' => false))
        ->add('gplusUid', null, array('required' => false))
        ->add('gplusName', null, array('required' => false))
        ->end()
        */
        if ($isSuperAdmin) {
            $formMapper
                ->end();
        }

        if ($isSuperAdmin) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                ->add('locked', null, array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                ->add('groups', ModelType::class, array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()
                /*
                ->with('Roles')
                ->add('realRoles', SecurityRolesType::class, array(
                    'label' => 'form.label_roles',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ))
                ->end()
                ->with('Keys')
                ->add('token', null, array('required' => false))
                ->add('twoStepVerificationCode', null, array('required' => false))
                ->end()
                */
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id', null, ['advanced_filter' => false])
            ->add('username', null, ['advanced_filter' => false])
            ->add('locked', null, ['advanced_filter' => false])
            ->add('email', null, ['advanced_filter' => false])
            ->add('groups', null, ['advanced_filter' => false]);
    }
}
