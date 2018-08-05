<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Notice;
use Application\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class NoticeAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    );

    protected $translationDomain = 'AppBundle';

    public function configure()
    {
        $this->parentAssociationMapping = 'client';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('text', null, [
                'label' => 'Текст',
                'required' => true,
            ])
            ->add('date', 'sonata_type_date_picker', [
                'view_timezone' => $this->getParameter('admin_view_timezone'),
                'label' => 'Дата',
                'format' => 'dd.MM.yyyy',
                'required' => true,
            ])
            ->add('viewed', 'checkbox', [
                'label' => 'Просмотрено',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('text', null, [
                'label' => 'Текст',
                'route' => ['name' => 'edit'],
            ])
            ->add('viewed', 'boolean', [
                'label' => 'Просмотрено',
                'editable' => true
            ])
            ->add('date', null, [
                'label' => 'Дата',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdAt', null, [
                'label' => 'Когда добавлено',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлено',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('date', 'doctrine_orm_date_range', ['label' => 'Дата', 'advanced_filter' => false,], 'sonata_type_date_range_picker',
                [
                    'field_options_start' => [
                        'label' => 'От',
                        'format' => 'dd.MM.yyyy'
                    ],
                    'field_options_end' => [
                        'label' => 'До',
                        'format' => 'dd.MM.yyyy'
                    ]
                ]
            )
            ->add('viewed', 'doctrine_orm_callback', [
                    'label' => 'Просмотрено',
                    'callback' => [$this, 'getViewedFilter'],
                    'field_type' => 'choice',
                    'field_options' => [
                        'label' => ' ',
                        'choices' => [
                            1 => 'Да',
                            2 => 'Нет',
                        ],
                    ],
                    'advanced_filter' => false,
                ]
            )
            ->add('createdBy', 'doctrine_orm_number', [
                    'label' => 'Кем добавлено',
                    'field_type' => 'text',
                    'advanced_filter' => false,
                ]
            )
            ->add('id', 'doctrine_orm_callback', [
                    'label' => 'id',
                    'callback' => [$this, 'getById'],
                    'field_type' => 'text',
                    'advanced_filter' => false,
                ]
            );
    }

    /**
     * @param $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getById($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        $queryString = null;
        $valueCount = count($value['value']);
        $valueIndex = 0;
        foreach ($value['value'] as $val) {
            $valueIndex++;
            if ($valueIndex !== $valueCount) {
                $orOperator = 'OR ';
            } else {
                $orOperator = '';
            }

            $queryString .= "$alias.id=$val $orOperator";
        }

        $queryBuilder->andWhere("($queryString)");

        return true;

    }

    /**
     * @param $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getViewedFilter($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        if ($value['value'] == 1) {
            $queryBuilder
                ->andWhere(':user MEMBER OF ' . $alias . '.viewedBy');
        }

        if ($value['value'] == 2) {
            $queryBuilder
                ->andWhere(':user NOT MEMBER OF ' . $alias . '.viewedBy');
        }

        $queryBuilder
            ->setParameter('user', $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser());

        return true;
    }

    /**
     * @param mixed $object
     */
    public function prePersist($object)
    {
        $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.context')
            ->getToken()
            ->getUser();

        $user = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.context')
            ->getToken()
            ->getUser();

        $this->processViewedBeforeSave($object, $user);
    }

    /**
     * @param mixed $object
     */
    public function preUpdate($object)
    {
        $user = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.token_storage')
            ->getToken()
            ->getUser();

        $this->processViewedBeforeSave($object, $user);
    }

    /**
     * @param Notice $notice
     * @param User $user
     */
    public function processViewedBeforeSave(Notice $notice, User $user)
    {
        if ($notice->getViewed()) {
            if (!$notice->getViewedBy()->contains($user)) {
                $notice->addViewedBy($user);
            }
        } else {
            if ($notice->getViewedBy()->contains($user)) {
                $notice->removeViewedBy($user);
            }
        }
    }

    public function getAutoNotices()
    {
        if ($this->getParent() != null) {
            return $this
                ->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Notice')
                ->getAutoNotices($this->getParent()->getSubject());
        }
    }
}
