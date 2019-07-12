<?php

namespace AppBundle\Admin;

use AppBundle\Entity\CertificateType;
use AppBundle\Entity\Client;
use AppBundle\Entity\ClientField;
use AppBundle\Entity\ClientFieldOption;
use AppBundle\Entity\ClientFieldValue;
use AppBundle\Entity\Contract;
use AppBundle\Entity\Document;
use AppBundle\Entity\DocumentFile;
use AppBundle\Entity\GeneratedDocument;
use AppBundle\Entity\MenuItem;
use AppBundle\Entity\Note;
use AppBundle\Entity\Service;
use AppBundle\Entity\ShelterHistory;
use AppBundle\Entity\Notice;
use AppBundle\Form\DataTransformer\ImageStringToFileTransformer;
use AppBundle\Form\Type\AppHomelessFromDateType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ClientAdmin extends BaseAdmin
{
    protected $searchResultActions = ['show'];

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'contracts.dateFrom',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param ShowMapper $showMapper
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Фото', ['class' => 'col-md-4 photo-block'])
            ->add('photo', null, [
                'label' => 'Фото',
                'template' => '/admin/fields/client_photo_show.html.twig',
            ])
            ->end();

        $showMapper
            ->with('Основная информация', ['class' => 'col-md-4'])
            ->add('fullname', null, [
                'label' => 'ФИО',
            ])
            ->add('id', 'text', [
                'label' => 'ID',
            ])
            ->add('gender', 'choice', [
                'label' => 'Пол',
                'choices' => [
                    Client::GENDER_MALE => 'Мужской',
                    Client::GENDER_FEMALE => 'Женский',
                ],
            ])
            ->add('birthDate', 'date', [
                'label' => 'Дата рождения',
            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
            ])
            ->add('isHomeless', null, [
                'label' => 'Статус',
                'template' => '/admin/fields/client_notIsHomeless.html.twig'
            ])
            ->add('createdAt', 'date', [
                'label' => 'Дата добавления',
            ]);
        /** @var Client $client */
        $client = $this->getSubject();
        if ($client->getShelterHistories()->count()) {
            $showMapper->add('shelterHistories', 'entity', [
                'label' => 'Проживание в приюте',
                'template' => '/admin/fields/client_shelter_histories_show.html.twig',
            ]);
        }
        $showMapper->end();

        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.context');
        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_SERVICE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_SERVICE_ADMIN_ALL')) {
            $showMapper
                ->with('Последние услуги', ['class' => 'col-md-4'])
                ->add('services', 'array', [
                    'label' => ' ',
                    'template' => '/admin/fields/client_services_show.html.twig'
                ])
                ->end();
        }


        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_NOTE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_NOTE_ADMIN_ALL')) {
            $showMapper
                ->with('Последние примечания')
                ->add('notes', 'array', [
                    'label' => ' ',
                    'template' => '/admin/fields/client_notes_show.html.twig'
                ])
                ->end();
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_CLIENT_ADMIN_EDIT') || $securityContext->isGranted('ROLE_APP_CLIENT_ADMIN_ALL')) {
            $showMapper
                ->with('Дополнительная информация', [
                    'class' => 'col-md-12',
                    'box_class' => 'box box-primary box-client-field-all',
                    'type' => 'additional-info',
                    'subtype' => 'main-block-start'
                ]);

            $showMapperAdditionalInfo = [];

            $showMapperAdditionalInfo[] = [
                'with' => ['   ', [
                    'class' => 'col-md-4',
                    'box_class' => 'box-client-field',
                    'type' => 'additional-info',
                    'subtype' => 'item'
                ]]
            ];

            // Дополнительные поля клиента
            if ($this->hasSubject()) {
                $fieldValues = $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('doctrine.orm.entity_manager')
                    ->getRepository(ClientFieldValue::class)
                    ->findByClient($this->getSubject());

                $blankTabName = '    ';
                foreach ($fieldValues as $key => $fieldValue) {
                    if (0 !== $key && 0 == $key % 1) {
                        $showMapperAdditionalInfo[] = [
                            'with' => [$blankTabName, [
                                'class' => 'col-md-4 additional-info-block',
                                'box_class' => 'box-client-field',
                                'type' => 'additional-info',
                                'subtype' => 'item'
                            ]],
                        ];
                        $blankTabName .= ' ';
                    }

                    $field = $fieldValue->getField();

                    $options = ['label' => $field->getName()];

                    if ($field->getType() == ClientField::TYPE_OPTION) {
                        $options['choices'] = $field->getOptionsArray();
                        if ($field->getMultiple()) {
                            $options['multiple'] = true;
                            $options['template'] = '/admin/fields/show_array.html.twig';
                        }
                    }

                    if ($field->getType() == ClientField::TYPE_FILE) {
                        $options['template'] = '/admin/fields/client_file_show.html.twig';
                    }

                    if ($field->getType() == ClientField::TYPE_TEXT) {
                        $options['template'] = '/admin/fields/client_text_show.html.twig';
                    }
                    if ($field->getCode() == 'homelessFrom') {
                        $options['pattern'] = 'MMM y';
                    }
                    $showMapperAdditionalInfo[count($showMapperAdditionalInfo) - 1]['add'] = ['additionalField' . $field->getCode(), $field->getShowFieldType(), $options];
                }
            }

            $showMapperAdditionalInfoSort = [];
            foreach ($showMapperAdditionalInfo as $key => $item) {
                $showMapperAdditionalInfoSort[$key % ceil(count($showMapperAdditionalInfo) / 3)][$key / ceil(count($showMapperAdditionalInfo) / 3)] = $item;
            }

            foreach ($showMapperAdditionalInfoSort as $showMapperAdditionalInfoSortItems) {
                foreach ($showMapperAdditionalInfoSortItems as $item) {
                    if (isset($item['add'])) {
                        $reflectionMethod = new \ReflectionMethod(ShowMapper::class, 'add');
                        $reflectionMethod->invokeArgs($showMapper, $item['add']);
                    }
                }
            }
            $showMapper->end();
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $em = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');
        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($em){
            $client = $event->getForm()->getData();

            if (!$client instanceof Client) {
                return;
            }
            if ($client->getisHomeless()) {
                /** @var $em EntityManagerInterface */
                $clientsFields = $em->getRepository(ClientField::class)->findAll();
                foreach ($clientsFields as $clientsField) {
                    /** @var $clientsField ClientField */
                    if ($clientsField->getMandatoryForHomeless()) {
                        $fieldForm = $event->getForm()->get('additionalField' . $clientsField->getCode());
                        if (!$fieldForm->getData() || ($fieldForm->getData() instanceof ArrayCollection && $fieldForm->getData()->count() === 0)) {
                            $event->getForm()->get('additionalField' . $clientsField->getCode())->addError(new FormError('Поле обязательное для заполнения'));
                        }
                    }
                }
            }

            foreach ($client->getFieldValues() as $fieldValue) {
                if (!$fieldValue instanceof ClientFieldValue) {
                    continue;
                }

                $field = $fieldValue->getField();

                if (!$field instanceof ClientField) {
                    continue;
                }

                if (!$field->getType() == ClientField::TYPE_OPTION) {
                    continue;
                }

                $options = $event->getForm()->getNormData()->additionalFieldValues;

                if (!isset($options[$field->getCode()])) {
                    continue;
                }

                foreach ($options[$field->getCode()] as $value) {
                    if (!$value instanceof ClientFieldOption) {
                        continue;
                    }

                    if ($value->getNotSingle() && 1 == count($options[$field->getCode()])) {
                        $event->getForm()->get('additionalField' . $field->getCode())->addError(new FormError("'{$value->getName()}' не может быть единственным ответом"));
                    }
                }

            }
        });

        $formMapper
            ->with('Основная информация')
            ->add('photo', 'app_photo', [
                'label' => 'Фото',
                'required' => false,
                'allow_delete' => false,
                'download_link' => false,
                'attr' => ['class' => 'client_photo_input'],
            ])
            ->add('lastname', null, [
                'label' => 'Фамилия',
                'required' => true,
            ])
            ->add('firstname', null, [
                'label' => 'Имя',
                'required' => true,
            ])
            ->add('middlename', null, [
                'label' => 'Отчество',
                'required' => true,
            ])
            ->add('gender', 'choice', [
                'label' => 'Пол',
                'required' => true,
                'choices' => [
                    Client::GENDER_MALE => 'Мужской',
                    Client::GENDER_FEMALE => 'Женский',
                ],
            ])
            ->add('birthDate', 'sonata_type_date_picker', [
                'dp_default_date' => (new \DateTime('-50 year'))->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата рождения',
                'required' => true,

            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
                'required' => true,
            ])
            ->add('notIsHomeless', CheckboxType::class, [
                'label' => 'Не бездомный',
                'label_attr'=> ['class'=> 'changeSelectinsData'],
                'required' => false,
            ])
            ->end();

        //дополнительные поля клиента
        $formMapper
            ->with('Дополнительная информация');

        /** @var ClientField[] $fields */
        $fields = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(ClientField::class)
            ->findByEnabledAll();

        foreach ($fields as $field) {
            $options = [
                'label' => $field->getName(),
                'required' => $field->getRequired(),
                'attr' => ["class" => ($field->getMandatoryForHomeless() ? 'mandatory-for-homeless' : '') . ' ' . (!$field->getEnabled() && $field->getEnabledForHomeless() ? 'enabled-for-homeless' : '')]
            ];

            switch ($field->getType()) {
                case ClientField::TYPE_OPTION:
                    $options['class'] = 'AppBundle\Entity\ClientFieldOption';

                    $options['query_builder'] = function (EntityRepository $er) use ($field) {
                        return $er->createQueryBuilder('o')
                            ->where('o.field = :field')
                            ->addOrderBy('o.sort', 'ASC')
                            ->setParameter('field', $field);
                    };

                    if ($field->getMultiple()) {
                        $options['multiple'] = true;
                    }
                    break;

                case ClientField::TYPE_DATETIME:
                    break;
            }

            $this->addClientField($formMapper, $field, $options);
        }

        if (!$this->getSubject()->getId()) {
            $formMapper
                ->add('compliance', CheckboxType::class, [
                    'label' => 'Заявление об обработке персональных данных заполнено',
                    'required' => true,
                ]);
        }

        $formMapper
            ->end();

        $formMapper->getFormBuilder()->get('photo')->addModelTransformer(new ImageStringToFileTransformer());
    }

    /**
     * @param mixed $object
     */
    public function prePersist($object)
    {
        $this->processFieldValues($object);
    }

    /**
     * @param mixed $object
     */
    public function preUpdate($object)
    {
        $this->processFieldValues($object, true);
    }

    /**
     * Создает нужные и удаляет ненужные объекты значений дополнительных полей
     * @param $object
     * @param bool $isUpdate
     */
    public function processFieldValues($object, $isUpdate = false)
    {
        $em = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $fieldRepo = $em->getRepository('AppBundle:ClientField');
        $fieldValueRepo = $em->getRepository('AppBundle:ClientFieldValue');

        if ($isUpdate) {
            foreach ($object->additionalFieldValuesToRemove as $code) {
                $fieldValue = $fieldValueRepo->findOneByClientAndFieldCode($object, $code);

                if ($fieldValue instanceof ClientFieldValue) {
                    $em->remove($fieldValue);
                }
            }

            unset($code, $fieldValue);
        }

        foreach ($object->additionalFieldValues as $code => $value) {
            $field = $fieldRepo->findOneBy(['code' => $code]);
            $fieldValue = null;

            if ($isUpdate) {
                $fieldValue = $fieldValueRepo->findOneByClientAndFieldCode($object, $code);
            }

            if (!$fieldValue instanceof ClientFieldValue) {
                if (!$field instanceof ClientField) {
                    continue;
                }

                $fieldValue = new ClientFieldValue();
                $fieldValue->setClient($object);
                $fieldValue->setField($field);
            }

            $fieldValue->setValue($value);

            $em->persist($fieldValue);
        }
    }

    /**
     * @param string $context
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        $params = $this->getFilterParameters();

        if (isset($params['_sort_by']) && $params['_sort_by'] === 'lastname') {
            $alias = $query->getRootAlias();

            $query
                ->resetDQLPart('orderBy')
                ->orderBy("CONCAT($alias.lastname,' ',$alias.firstname,' ',$alias.middlename)", $params['_sort_order']);
        }

        return $query;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('lastContractDuration', 'number', [
                'template' => '/admin/fields/client_contract_duration_list.html.twig',
                'label' => ' ',
            ])
            ->addIdentifier('id', 'number', ['route' => ['name' => 'show']])
            ->addIdentifier('lastname', null, [
                'label' => 'ФИО',
                'template' => '/admin/fields/client_fullname_list.html.twig',
                'route' => ['name' => 'show'],
            ])
            ->add('birthDate', 'date', [
                'label' => 'Дата рождения'
            ])
            ->add('contracts.dateFrom', null, [
                'template' => '/admin/fields/client_contract_list.html.twig',
                'label' => 'Договор'
            ])
            ->add('createdAt', 'date', [
                'label' => 'Когда добавлен',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('search', 'doctrine_orm_callback', [
                    'label' => 'Поиск',
                    'callback' => [$this, 'getClientSearchFilter'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('lastName', 'doctrine_orm_callback', [
                    'label' => 'Фамилия',
                    'callback' => [$this, 'getClientSearchLastName'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('firstName', 'doctrine_orm_callback', [
                    'label' => 'Имя',
                    'callback' => [$this, 'getClientSearchFirstName'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('middleName', 'doctrine_orm_callback', [
                    'label' => 'Отчество',
                    'callback' => [$this, 'getClientSearchMiddleName'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('note', 'doctrine_orm_callback', [
                    'label' => 'Примечание',
                    'callback' => [$this, 'getClientSearchNote'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('contract', 'doctrine_orm_callback', [
                    'label' => 'Сервистный план',
                    'callback' => [$this, 'getClientSearchContract'],
                    'field_type' => 'text',
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('birthDate', 'doctrine_orm_date_range', ['label' => 'Дата рождения', 'advanced_filter' => false,], 'sonata_type_date_range_picker',
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
            ->add('contractCreatedBy', 'doctrine_orm_callback', [
                    'label' => 'Кем добавлен договор',
                    'callback' => [$this, 'getContractCreatedByFilter'],
                    'field_type' => 'entity',
                    'field_options' => [
                        'class' => 'Application\Sonata\UserBundle\Entity\User',
                        'property' => 'fullname',
                        'multiple' => false,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->select('u')
                                ->orderBy('CONCAT(u.lastname, u.firstname, u.middlename)', 'ASC');
                        },
                    ],
                    'advanced_filter' => false,
                ]
            )
            ->add('contractStatus', 'doctrine_orm_callback', [
                    'label' => 'Статус договора',
                    'callback' => [$this, 'getContractStatusFilter'],
                    'field_type' => 'entity',
                    'field_options' => [
                        'class' => 'AppBundle\Entity\ContractStatus',
                        'property' => 'name',
                        'multiple' => true,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.name', 'ASC');
                        },
                    ],
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
    public function getContractCreatedByFilter($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $queryBuilder->leftJoin($alias . '.contracts', 'c1');
        $queryBuilder->leftJoin('c1.createdBy', 'u1');
        $queryBuilder->andWhere('u1.id = :userId');
        $queryBuilder->setParameter('userId', $value['value']);

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
    public function getContractStatusFilter($queryBuilder, $alias, $field, $value)
    {
        if ($value['value']->isEmpty()) {
            return;
        }

        if (!in_array('c1', $queryBuilder->getAllAliases())) {
            $queryBuilder->leftJoin($alias . '.contracts', 'c1');
        }

        $queryBuilder->leftJoin('c1.status', 's1');
        $queryBuilder->andWhere('s1.id IN (:statuses)');
        $queryBuilder->setParameter('statuses', array_values($value['value']->toArray()));

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getClientSearchFilter($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $queryBuilder->leftJoin($alias . '.notes', 'n');

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.lastname LIKE :word$key OR $alias.firstname LIKE :word$key OR $alias.middlename LIKE :word$key OR $alias.id LIKE :word$key OR $alias.birthDate LIKE :word$key OR n.text LIKE :word$key)");
                $queryBuilder->orderBy("CUSTOM_PART(
                    CASE
                        WHEN $alias.lastname like :word$key THEN 0
                        WHEN $alias.firstname like :word$key THEN 1
                        WHEN $alias.middlename like :word$key THEN 2
                        WHEN $alias.id like :word$key THEN 3
                        WHEN $alias.birthDate like :word$key THEN 4
                        WHEN n.text like :word$key THEN 5
                        ELSE 6 END)", "ASC");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getClientSearchLastName($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.lastname LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getClientSearchFirstName($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.firstname LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     *
     * @return bool|void
     */
    public function getClientSearchMiddleName($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.middlename LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     * @return bool|void
     */
    public function getClientSearchNote($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $queryBuilder->leftJoin($alias . '.notes', 'sn');

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("(sn.text LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }
        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     * @return bool|void
     */
    public function getClientSearchContract($queryBuilder, $alias, $field, $value)
    {
        if (null === $value['value']) {
            return;
        }

        $queryBuilder->leftJoin($alias . '.contracts', 'con');
        $queryBuilder->leftJoin('con.items', 'i');

        $words = explode(' ', $value['value']);

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("(con.comment LIKE :word$key OR i.comment LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    /**
     * @param MenuItemInterface $menu
     * @param string $action
     * @param AdminInterface|null $childAdmin
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['show', 'edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.context');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_DOCUMENT_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_DOCUMENT_ADMIN_ALL')) {
            $menu->addChild(
                'Документы',
                ['uri' => $admin->generateUrl('app.document.admin.list', ['id' => $id])]
            );
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_DOCUMENT_FILE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_DOCUMENT_FILE_ADMIN_ALL')) {
            $menu->addChild(
                'Файлы',
                ['uri' => $admin->generateUrl('app.document_file.admin.list', ['id' => $id])]
            );
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_CONTRACT_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_CONTRACT_ADMIN_ALL')) {
            $menu->addChild(
                'Сервисные планы',
                ['uri' => $admin->generateUrl('app.contract.admin.list', ['id' => $id])]
            );
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_SHELTER_HISTORY_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_SHELTER_HISTORY_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled('shelter_history') && $this->isMenuItemEnabledShelterHistory($id)) {
                $menu->addChild(
                    'Проживание в приюте',
                    ['uri' => $admin->generateUrl('app.shelter_history.admin.list', ['id' => $id])]
                );
            }
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled('shelter_history') && $this->isMenuItemEnabledShelterHistory($id)) {
                $menu->addChild(
                    'Анкета',
                    ['uri' => $admin->generateUrl('app.resident_questionnaire.admin.list', ['id' => $id])]
                );
            }
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_CERTIFICATE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_CERTIFICATE_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled('certificate')) {
                $menu->addChild(
                    'Выдать справку',
                    ['uri' => $admin->generateUrl('app.certificate.admin.list', ['id' => $id])]
                );
            }
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_GENERATED_DOCUMENT_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_GENERATED_DOCUMENT_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled('generated_document')) {
                $menu->addChild(
                    'Построить документ',
                    ['uri' => $admin->generateUrl('app.generated_document.admin.list', ['id' => $id])]
                );
            }
        }

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('ROLE_APP_NOTICE_ADMIN_LIST') || $securityContext->isGranted('ROLE_APP_NOTICE_ADMIN_ALL')) {
            $user = $this
                ->getConfigurationPool()
                ->getContainer()
                ->get('security.token_storage')
                ->getToken()
                ->getUser();

        $noticesCount = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(Notice::class)
            ->getUnviewedCount($this->getSubject(), $user);

        $noticesCount += count(
            $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository(Notice::class)
                ->getAutoNotices($this->getSubject())
        );

            $menu->addChild(
                'Напоминания' . ($noticesCount > 0 ? " ($noticesCount)" : ''),
                ['uri' => $admin->generateUrl('app.notice.admin.list', ['id' => $id, 'filter' => ['date' => ['value' => ['end' => date('d.m.Y')]], 'viewed' => ['value' => 2]]])]
            );
        }
    }

    /**
     * @param $menuItemCode
     *
     * @return bool
     */
    public function isMenuItemEnabled($menuItemCode)
    {
        $menuItem = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:MenuItem')
            ->findOneBy(['code' => $menuItemCode]);

        if (!$menuItem instanceof MenuItem) {
            return false;
        }

        return $menuItem->getEnabled();
    }

    public function isMenuItemEnabledShelterHistory($client)
    {
        return !!$this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ShelterHistory')->findOneBy(['client' => $client]);
    }

    /**
     * Возвращает массив для использования как значение опции choices в конструкторе поля формы
     *
     * @param ClientField $field
     * @return array
     */
    private function getChoices(ClientField $field)
    {
        $result = [];

        foreach ($field->getOptions() as $clientFieldOption) {
            $result[$clientFieldOption->getId()] = $clientFieldOption->getName();
        }

        return $result;
    }

    /**
     * Возвращает массив опций поля формы для конфигурирования типа поля ChoiceFieldMaskType,
     * с удаленными лишними опциями и добавленной опцией choices
     *
     * @param array $options
     * @param ClientField $field
     *
     * @return array
     */
    private function getProcessedOptionsForChoiceFieldMaskTypeField(array $options, ClientField $field)
    {
        unset($options['class'], $options['query_builder']);

        $options['choices'] = $this->getChoices($field);

        return $options;
    }

    /**
     * Добавляет поле типа ChoiceFieldMaskType к $formMapper
     *
     * @param FormMapper $formMapper
     * @param ClientField $field
     * @param array $options
     */
    private function addChoiceFieldMaskTypeField(FormMapper $formMapper, ClientField $field, array $options)
    {
        $options = $this->getProcessedOptionsForChoiceFieldMaskTypeField($options, $field);
        $transformer = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('app.additional_field_to_array.transformer');

        $formMapper->add('additionalField' . $field->getCode(), ChoiceFieldMaskType::class, $options);
        $formMapper->getFormBuilder()->get('additionalField' . $field->getCode())->addModelTransformer($transformer);
    }

    /**
     * Реализация зависимости отображения доп. полей формы в зависимости друг от друга
     *
     * @param FormMapper $formMapper
     * @param ClientField $field
     * @param array $options
     */
    private function addClientField(FormMapper $formMapper, ClientField $field, array $options)
    {
        switch ($field->getCode()) {
            case 'citizenship':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другое', $field) => ['additionalFieldcitizenshipOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'pensioner':
                $options['map'] = [
                    $this->getFieldOptionValueId('Да', $field) => ['additionalFieldpensionReason'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'pensionReason':
                $options['map'] = [
                    $this->getFieldOptionValueId('По инвалидности', $field) => ['additionalFielddisabilityGroup'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'student':
                $options['map'] = [
                    $this->getFieldOptionValueId('Да', $field) => ['additionalFieldprofession'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'liveInHousing':
                $options['map'] = [
                    $this->getFieldOptionValueId('Да', $field) => ['additionalFieldhousing'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'housing':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другое', $field) => ['additionalFieldhousingOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'registration':
                $options['map'] = [
                    $this->getFieldOptionValueId('Да', $field) => ['additionalFieldregistrationPlace'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'homelessReason':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другие', $field) => ['additionalFieldhomelessReasonOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'breadwinner':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другие', $field) => ['additionalFieldbreadwinnerOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'breadwinnerMain':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другие', $field) => ['additionalFieldbreadwinnerOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'disease':
                $options['map'] = [
                    $this->getFieldOptionValueId('Другие', $field) => ['additionalFielddiseaseOther'],
                ];
                $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
                break;

            case 'homelessFrom':
                $options['years'] = range(date('Y'), date('Y') - 100);
                $formMapper
                    ->add('additionalField' . $field->getCode(), AppHomelessFromDateType::class, $options);
                break;

            default:
                $formMapper
                    ->add('additionalField' . $field->getCode(), $field->getFormFieldType(), $options);
                break;
        }
    }

    /**
     * Получение идентификатора значения свойства дополнительного поля
     *
     * @param $valueName
     * @param $field
     *
     * @return null|object
     */
    private function getFieldOptionValueId($valueName, $field)
    {
        $em = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $fieldOptionRepo = $em->getRepository('AppBundle:ClientFieldOption');
        $value = ($fieldOptionRepo->findOneBy(['name' => $valueName, 'field' => $field]));

        return null !== $value ? $value->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        unset($list['create'], $list['list']);

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'show':
                $name = 'AppBundle:Admin\Client:base_show.html.twig';
                break;
            case 'edit':
                $name = 'AppBundle:Admin\Client:base_edit.html.twig';
                break;
            default:
                $name = parent::getTemplate($name);
                break;
        }

        return $name;
    }
}
