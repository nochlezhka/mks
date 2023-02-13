<?php

namespace App\Admin;

use App\Controller\ClientController;
use App\Entity\Client;
use App\Entity\ClientField;
use App\Entity\ClientFieldOption;
use App\Entity\ClientFieldValue;
use App\Entity\MenuItem;
use App\Entity\ShelterHistory;
use App\Entity\User;
use App\Form\DataTransformer\AdditionalFieldToArrayTransformer;
use App\Form\Type\AppHomelessFromDateType;
use App\Repository\ClientFieldRepository;
use App\Repository\ClientFieldValueRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Knp\Menu\ItemInterface as MenuItemInterface;
use ReflectionException;
use ReflectionMethod;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Клиенты',
    'model_class' => Client::class,
    'controller'=> ClientController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class ClientAdmin extends BaseAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'contracts.dateFrom',
    ];

    protected string $translationDomain = 'App';

    /**
     * Мапа с описанием дополнительных полей, отображение которых зависит от значения других полей.
     * (`ChoiceFieldMaskType`).
     * Названия зависимых и определяющих полей начинается с `additionalField`
     *
     * Формат такой:
     * ```
     * ["название зависимого поля" =>
     *      ["название choice поля" =>
     *          ["значение, при котором зависимое поле показывается" => true],
     *      ],
     *  ...
     * ]
     * ```
     *
     * @var array
     */
    private array $dependantFields = [];

    /**
     * Мапа с описанием полей, от которых зависит отображение других полей (`ChoiceFieldMaskType`).
     * Названия зависимых и определяющих полей начинается с `additionalField`
     *
     * Формат такой:
     * ```
     * ["название choice поля" =>
     *      ["значение, при котором показываются зависимые поля" =>
     *          ["название зависимого поля", ...],
     *      ],
     *  ...
     * ]
     * ```
     *
     * @var array
     */
    private array $choiceTypeMaps = [];

    private AdditionalFieldToArrayTransformer $additionalFieldToArrayTransformer;

    private AuthorizationCheckerInterface $authorizationChecker;

    private ClientFieldRepository $clientFieldRepository;
    private ClientFieldValueRepository $clientFieldValueRepository;

    public function __construct(
        NoteAdmin $noteAdmin,
        ServiceAdmin $serviceAdmin,
        DocumentAdmin $documentAdmin,
        DocumentFileAdmin $documentFileAdmin,
        ContractAdmin $contractAdmin,
        ShelterHistoryAdmin $shelterHistoryAdmin,
        ResidentQuestionnaireAdmin $residentQuestionnaireAdmin,
        CertificateAdmin $certificateAdmin,
        GeneratedDocumentAdmin $generatedDocumentAdmin,
        NoticeAdmin $noticeAdmin,
        HistoryDownloadAdmin $historyDownloadAdmin,
        ResidentFormResponseAdmin $residentFormResponseAdmin,
        AdditionalFieldToArrayTransformer $additionalFieldToArrayTransformer,
        AuthorizationCheckerInterface $authorizationChecker,
        ClientFieldRepository $clientFieldRepository,
        ClientFieldValueRepository $clientFieldValueRepository
    )
    {
        $this->addChild($noteAdmin, 'client');
        $this->addChild($serviceAdmin, 'client');
        $this->addChild($documentAdmin, 'client');
        $this->addChild($documentFileAdmin, 'client');
        $this->addChild($contractAdmin, 'client');
        $this->addChild($shelterHistoryAdmin, 'client');
        $this->addChild($residentQuestionnaireAdmin, 'client');
        $this->addChild($certificateAdmin, 'client');
        $this->addChild($generatedDocumentAdmin, 'client');
        $this->addChild($noticeAdmin, 'client');
        $this->addChild($historyDownloadAdmin, 'client');
        $this->addChild($residentFormResponseAdmin, 'client');
        $this->additionalFieldToArrayTransformer = $additionalFieldToArrayTransformer;
        $this->authorizationChecker = $authorizationChecker;
        $this->clientFieldRepository = $clientFieldRepository;
        $this->clientFieldValueRepository = $clientFieldValueRepository;
        parent::__construct();
    }

    /**
     * @throws ReflectionException
     */
    public function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('photo', ['class' => 'col-md-4 photo-block'])
            ->add('photo', null, [
                'label' => 'Фото',
                'template' => '/admin/fields/client_photo_show.html.twig',
            ])
            ->end();

        $show
            ->with('base_info', ['class' => 'col-md-4'])
            ->add('fullname', null, [
                'label' => 'ФИО',
            ])
            ->add('id', TextType::class, [
                'label' => 'ID',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'choices' => [
                    'Мужской' => Client::GENDER_MALE,
                    'Женский' => Client::GENDER_FEMALE,
                ],
            ])
            ->add('birthDate', 'date', [
                'label' => 'Дата рождения',
            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
            ]);
        if ($this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS)) {
            $show->add('isHomeless', null, [
                'label' => 'Статус',
                'template' => '/admin/fields/client_notIsHomeless.html.twig',
            ]);
        }
        $show->add('createdAt', 'date', [
            'label' => 'Дата добавления',
        ]);
        if ($this->isMenuItemEnabled(MenuItem::CODE_SHELTER_HISTORY)) {
            $show->add('shelterHistories', EntityType::class, [
                'label' => 'Проживание в приюте',
                'template' => '/admin/fields/client_shelter_histories_show.html.twig',
            ]);
        }
        $show->end();

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_SERVICE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_SERVICE_ADMIN_ALL')) {
            $show
                ->with('last_services', ['class' => 'col-md-4'])
                ->add('services', 'array', [
                    'label' => ' ',
                    'template' => '/admin/fields/client_services_show.html.twig',
                ])
                ->end();
        }


        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_NOTE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_NOTE_ADMIN_ALL')) {
            $show
                ->with('last_notes')
                ->add('notes', 'array', [
                    'label' => ' ',
                    'template' => '/admin/fields/client_notes_show.html.twig',
                ])
                ->end();
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_CLIENT_ADMIN_EDIT') || $this->authorizationChecker->isGranted('ROLE_APP_CLIENT_ADMIN_ALL')) {
            $show
                ->with('additional_info', [
                    'class' => 'col-md-12',
                    'box_class' => 'box box-primary box-client-field-all',
                    'type' => 'additional-info',
                    'subtype' => 'main-block-start',
                ]);

            $showMapperAdditionalInfo = [];

            $showMapperAdditionalInfo[] = [
                'with' => ['   ', [
                    'class' => 'col-md-4',
                    'box_class' => 'box-client-field',
                    'type' => 'additional-info',
                    'subtype' => 'item',
                ]],
            ];

            // Дополнительные поля клиента
            if ($this->hasSubject()) {
                $fieldValues = $this
                    ->clientFieldValueRepository
                    ->findByClient($this->getSubject());

                $blankTabName = '    ';
                foreach ($fieldValues as $key => $fieldValue) {
                    if (0 !== $key) {
                        $showMapperAdditionalInfo[] = [
                            'with' => [$blankTabName, [
                                'class' => 'col-md-4 additional-info-block',
                                'box_class' => 'box-client-field',
                                'type' => 'additional-info',
                                'subtype' => 'item',
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
                    $showMapperAdditionalInfo[count($showMapperAdditionalInfo) - 1]['add'] = [self::getAdditionalFieldName($field->getCode()), $field->getShowFieldType(), $options];
                }
            }

            $showMapperAdditionalInfoSort = [];
            foreach ($showMapperAdditionalInfo as $key => $item) {
                $showMapperAdditionalInfoSort[$key % ceil(count($showMapperAdditionalInfo) / 3)][$key / ceil(count($showMapperAdditionalInfo) / 3)] = $item;
            }

            foreach ($showMapperAdditionalInfoSort as $showMapperAdditionalInfoSortItems) {
                foreach ($showMapperAdditionalInfoSortItems as $item) {
                    if (isset($item['add'])) {
                        $reflectionMethod = new ReflectionMethod(ShowMapper::class, 'add');
                        $reflectionMethod->invokeArgs($show, $item['add']);
                    }
                }
            }
            $show->end();
        }
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $client = $event->getForm()->getData();

            if (!$client instanceof Client) {
                return;
            }

            // проверяем заполненность обязательных доп. полей
            /** @var $em EntityManagerInterface */
            $clientsFields = $this->clientFieldRepository->findAll();
            $statusHomelessEnabled = $this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS);
            foreach ($clientsFields as $clientsField) {
                /** @var $clientsField ClientField */
                $addFieldName = self::getAdditionalFieldName($clientsField->getCode());
                $isRequired = $clientsField->getRequired()
                    || $statusHomelessEnabled && $clientsField->getMandatoryForHomeless() && $client->getisHomeless();
                if (!$isRequired) {
                    continue;
                }
                $newVal = $event->getForm()->get($addFieldName)->getData();
                if (self::isAdditionalFieldValueEmpty($newVal)
                    // не требуем заполненности скрытых полей
                    && $this->fieldCanBeShown($clientsField, $event->getForm())
                    // если редактируется старый клиент, и значение в БД уже пустое - прощаем
                    && !$this->canAdditionalFieldRemainEmpty($clientsField)
                ) {
                    $event->getForm()->get($addFieldName)->addError(new FormError('Поле обязательное для заполнения'));
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
                        $event->getForm()->get(self::getAdditionalFieldName($field->getCode()))->addError(new FormError("'{$value->getName()}' не может быть единственным ответом"));
                    }
                }

            }
        });

        $form
            ->with('base_info')
            ->add('photo', 'App\Form\Type\AppPhotoType', [
                'label' => 'Фото',
                'required' => false,
                'allow_delete' => false,
                'download_link' => false,
                'attr' => ['class' => 'client_photo_file'],
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
            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'required' => true,
                'choices' => [
                    'Мужской' => Client::GENDER_MALE,
                    'Женский' => Client::GENDER_FEMALE,
                ],
            ])
            ->add('birthDate', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new DateTime('-50 year'))->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата рождения',
                'required' => true,
            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
                'required' => true,
            ]);
        if ($this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS)) {
            $form->add('notIsHomeless', CheckboxType::class, [
                'label' => 'Не бездомный',
                'label_attr' => ['class' => 'changeSelectinsData'],
                'required' => false,
            ]);
        }
        $form->end();

        //дополнительные поля клиента
        $form
            ->with('additional_info');

        $fields = $this
            ->clientFieldRepository
            ->findByEnabledAll();

        $this->initFieldDependencies($fields);
        foreach ($fields as $field) {
            $options = [
                'label' => $field->getName(),
                'required' => $field->getRequired(),
                'attr' => ["class" => ($field->getMandatoryForHomeless() ? 'mandatory-for-homeless' : '') . ' ' . (!$field->getEnabled() && $field->getEnabledForHomeless() ? 'enabled-for-homeless' : '')],
            ];
            // если скрываемое поле раньше не было обязательным, разрешаем ему оставаться пустым
            // (это также поддержано в валидации в обработчике `FormEvents::SUBMIT`)
            if ($this->canAdditionalFieldRemainEmpty($field)) {
                $options['required'] = false;
            }

            switch ($field->getType()) {
                case ClientField::TYPE_OPTION:
                    $options['class'] = 'App\Entity\ClientFieldOption';

                    $options['query_builder'] = function (EntityRepository $er) use ($field) {
                        return $er->createQueryBuilder('o')
                            ->where('o.field = :field')
                            ->addOrderBy('o.sort', 'ASC')
                            ->setParameter('field', $field);
                    };

                    if ($field->getMultiple()) {
                        $options['multiple'] = true;
                    }
                    // когда у селекта выставлен `required`, по-умолчанию выбирается первый элемент из списка
                    // Это может быть нежелательно для скрываемых полей - мы делаем их необязательными, если они скрыты
                    // Чтобы избежать незаметной отправки непустого значения, указываем `placeholder`
                    if ($this->isAdditionalFieldDependant($field) && $options['required']) {
                        $options['placeholder'] = '';
                    }
                    break;

                case ClientField::TYPE_DATETIME:
                    break;
            }

            $this->addClientField($form, $field, $options);
        }

        if (!$this->getSubject()->getId()) {
            $form
                ->add('compliance', CheckboxType::class, [
                    'label' => 'Заявление об обработке персональных данных заполнено',
                    'required' => true,
                ]);
        }

        $form
            ->end();
    }

    /**
     * Можно ли не заполнять доп. поле.
     * Разрешаем не заполнять обязательные поля, если редактируется старый клиент, и в БД значение уже пустое.
     *
     * @param ClientField $field
     * @return bool
     */
    private function canAdditionalFieldRemainEmpty(ClientField $field): bool
    {
        if ($this->isAdditionalFieldDependant($field) && $this->getSubject()->getId()) {
            $curVal = $this->getSubject()->getAdditionalFieldValue($field->getCode());
            return self::isAdditionalFieldValueEmpty($curVal);
        }
        return false;
    }

    private static function isAdditionalFieldValueEmpty(mixed $val): bool
    {
        return $val instanceof Collection ? $val->count() == 0 : !$val;
    }

    public function prePersist(object $object): void
    {
        $this->processFieldValues($object);
    }

    /**
     * @param mixed $object
     */
    public function preUpdate(object $object): void
    {
        $this->processFieldValues($object, true);
    }

    /**
     * Создает нужные и удаляет ненужные объекты значений дополнительных полей
     */
    public function processFieldValues(object $object, bool $isUpdate = false): void
    {
        if(!($object instanceof Client)) return;

        if ($isUpdate) {
            foreach ($object->additionalFieldValuesToRemove as $code) {
                $fieldValue = $this->clientFieldValueRepository->findOneByClientAndFieldCode($object, $code);

                if ($fieldValue instanceof ClientFieldValue) {
                    $this->manager->getManager()->remove($fieldValue);
                }
            }

            unset($code, $fieldValue);
        }

        foreach ($object->additionalFieldValues as $code => $value) {
            $field = $this->clientFieldRepository->findOneBy(['code' => $code]);
            $fieldValue = null;

            if ($isUpdate) {
                $fieldValue = $this->clientFieldValueRepository->findOneByClientAndFieldCode($object, $code);
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

            $this->manager->getManager()->persist($fieldValue);
        }
    }


    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
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
                'label' => 'Дата рождения',
            ])
            ->add('contracts', null, [
                'template' => '/admin/fields/client_contract_list.html.twig',
                'label' => 'Договор',
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
                ],
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('search', CallbackFilter::class, [
                    'label' => 'Поиск',
                    'callback' => [$this, 'getClientSearchFilter'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('lastName', CallbackFilter::class, [
                    'label' => 'Фамилия',
                    'callback' => [$this, 'getClientSearchLastName'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('firstName', CallbackFilter::class, [
                    'label' => 'Имя',
                    'callback' => [$this, 'getClientSearchFirstName'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('middleName', CallbackFilter::class, [
                    'label' => 'Отчество',
                    'callback' => [$this, 'getClientSearchMiddleName'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('note', CallbackFilter::class, [
                    'label' => 'Примечание',
                    'callback' => [$this, 'getClientSearchNote'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('contract', CallbackFilter::class, [
                    'label' => 'Сервисный план',
                    'callback' => [$this, 'getClientSearchContract'],
                    'field_type' => TextType::class,
                    'global_search' => true,
                    'advanced_filter' => false,
                ]
            )
            ->add('birthDate', DateRangeFilter::class,
                [
                    'label' => 'Дата рождения',
                    'advanced_filter' => false,
                ],
                [
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'От',
                            'format' => 'dd.MM.yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'До',
                            'format' => 'dd.MM.yyyy',
                        ]
                    ]
                ]
            )
            ->add('contractCreatedBy', CallbackFilter::class, [
                    'label' => 'Кем добавлен договор',
                    'callback' => [$this, 'getContractCreatedByFilter'],
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => 'App\Entity\User',
                        'choice_label' => 'fullname',
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
            ->add('contractStatus', CallbackFilter::class, [
                    'label' => 'Статус договора',
                    'callback' => [$this, 'getContractStatusFilter'],
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => 'App\Entity\ContractStatus',
                        'choice_label' => 'name',
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

    public function getContractCreatedByFilter(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias . '.contracts', 'c1');
        $queryBuilder->leftJoin('c1.createdBy', 'u1');
        $queryBuilder->andWhere('u1.id = :userId');
        $queryBuilder->setParameter('userId', $data->getValue());

        return true;
    }

    public function getContractStatusFilter(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        if (!in_array('c1', $queryBuilder->getAllAliases())) {
            $queryBuilder->leftJoin($alias . '.contracts', 'c1');
        }

        $queryBuilder->leftJoin('c1.status', 's1');
        $queryBuilder->andWhere('s1.id IN (:statuses)');
        $queryBuilder->setParameter('statuses', array_values($data->getValue()->toArray()));

        return true;
    }

    public function getClientSearchFilter(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias . '.notes', 'n');

        $words = explode(' ', $data->hasValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.lastname LIKE :word$key OR $alias.firstname LIKE :word$key OR $alias.middlename LIKE :word$key OR $alias.id LIKE :word$key OR $alias.birthDate LIKE :word$key OR n.text LIKE :word$key)");
                $queryBuilder->orderBy("$alias.lastname, $alias.firstname, $alias.middlename", "ASC");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    public function getClientSearchLastName(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $words = explode(' ', $data->getValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.lastname LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    public function getClientSearchFirstName(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $words = explode(' ', $data->getValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.firstname LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    public function getClientSearchMiddleName(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if ($data->hasValue()) {
            return false;
        }

        $words = explode(' ', $data->getValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("($alias.middlename LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    public function getClientSearchNote(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if ($data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias . '.notes', 'sn');

        $words = explode(' ', $data->getValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("(sn.text LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }
        return true;
    }

    public function getClientSearchContract(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if ($data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias . '.contracts', 'con');
        $queryBuilder->leftJoin('con.items', 'i');

        $words = explode(' ', $data->getValue());

        foreach ($words as $key => $word) {
            $word = trim($word);
            if (!empty($word)) {
                $queryBuilder->andWhere("(con.comment LIKE :word$key OR i.comment LIKE :word$key)");
                $queryBuilder->setParameter("word$key", "%$word%");
            }
        }

        return true;
    }

    protected function configureTabMenu(
        MenuItemInterface $menu, string $action, ?AdminInterface $childAdmin = null
    ): void
    {
        if (!$childAdmin && !in_array($action, ['show', 'edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_DOCUMENT_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_DOCUMENT_ADMIN_ALL')) {
            $menu->addChild(
                'Документы',
                ['uri' => $admin->generateUrl(DocumentAdmin::class.'.list', ['id' => $id])]
            );
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_DOCUMENT_FILE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_DOCUMENT_FILE_ADMIN_ALL')) {
            $menu->addChild(
                'Файлы',
                ['uri' => $admin->generateUrl(DocumentFileAdmin::class.'.list', ['id' => $id])]
            );
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_CONTRACT_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_CONTRACT_ADMIN_ALL')) {
            $menu->addChild(
                'Сервисные планы',
                ['uri' => $admin->generateUrl(ContractAdmin::class.'.list', ['id' => $id])]
            );
        }
        if ($this->isMenuItemEnabled(MenuItem::CODE_SHELTER_HISTORY) && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_SHELTER_HISTORY_ADMIN_LIST') ||$this->authorizationChecker->isGranted('ROLE_APP_SHELTER_HISTORY_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_SHELTER_HISTORY) && $this->isMenuItemEnabledShelterHistory($id)) {
                $menu->addChild(
                    'Проживание в приюте',
                    ['uri' => $admin->generateUrl(ShelterHistoryAdmin::class.'.list', ['id' => $id])]
                );
            }
        }

        $clientFormsEnabled = $this->metaService->isClientFormsEnabled();
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_QUESTIONNAIRE_LIVING) && $this->isMenuItemEnabledShelterHistory($id)) {
                $name = $clientFormsEnabled ? 'Старая анкета' : 'Анкета';
                $menu->addChild(
                    $name,
                    ['uri' => $admin->generateUrl(ResidentQuestionnaireAdmin::class.'.list', ['id' => $id])]
                );
            }
        }
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_RESIDENT_FORM_RESPONSE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_RESIDENT_FORM_RESPONSE_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_QUESTIONNAIRE_LIVING) && $this->isMenuItemEnabledShelterHistory($id)) {
                $name = $clientFormsEnabled ? 'Анкета' : 'Новая анкета';
                $menu->addChild(
                    $name,
                    ['uri' => $admin->generateUrl(ResidentFormResponseAdmin::class.'.list', ['id' => $id])]
                );
            }
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_CERTIFICATE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_CERTIFICATE_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_CERTIFICATE)) {
                $menu->addChild(
                    'Выдать справку',
                    ['uri' => $admin->generateUrl(CertificateAdmin::class.'.list', ['id' => $id])]
                );
            }
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_GENERATED_DOCUMENT_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_GENERATED_DOCUMENT_ADMIN_ALL')) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_GENERATED_DOCUMENT)) {
                $menu->addChild(
                    'Построить документ',
                    ['uri' => $admin->generateUrl(GeneratedDocumentAdmin::class.'.list', ['id' => $id])]
                );
            }
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_APP_NOTICE_ADMIN_LIST') || $this->authorizationChecker->isGranted('ROLE_APP_NOTICE_ADMIN_ALL')) {
            $user = $this
                ->tokenStorage
                ->getToken()
                ->getUser();

            if(!($user instanceof User)) {
                throw new InvalidArgumentException("Unexpected User type");
            }

            $noticesCount = $this->noticeRepository->getUnviewedCount($this->getSubject(), $user);

            $menu->addChild(
                'Напоминания' . ($noticesCount > 0 ? " ($noticesCount)" : ''),
                ['uri' => $admin->generateUrl(NoticeAdmin::class.'.list', ['id' => $id, 'filter' => ['date' => ['value' => ['end' => date('d.m.Y')]], 'viewed' => ['value' => 2]]])]
            );
        }
    }

    /**
     * @param $menuItemCode
     *
     * @return bool
     */
    public function isMenuItemEnabled($menuItemCode): bool
    {
        return $this->menuItemRepository->isEnableCode($menuItemCode);
    }

    public function isMenuItemEnabledShelterHistory($client): bool
    {
        return !!$this->manager->getRepository(ShelterHistory::class)->findOneBy(['client' => $client]);
    }

    /**
     * Возвращает массив для использования как значение опции choices в конструкторе поля формы
     *
     */
    private function getChoices(ClientField $field): array
    {
        $result = [];

        foreach ($field->getOptions() as $clientFieldOption) {
            $result[$clientFieldOption->getName()] = $clientFieldOption->getId();
        }

        return $result;
    }

    /**
     * Возвращает массив опций поля формы для конфигурирования типа поля ChoiceFieldMaskType,
     * с удаленными лишними опциями и добавленной опцией choices
     *
     */
    private function getProcessedOptionsForChoiceFieldMaskTypeField(array $options, ClientField $field): array
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
        $transformer = $this->additionalFieldToArrayTransformer;
        $formMapper->add(self::getAdditionalFieldName($field->getCode()), ChoiceFieldMaskType::class, $options);
        $formMapper->getFormBuilder()->get(self::getAdditionalFieldName($field->getCode()))->addModelTransformer($transformer);
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
        $addFieldName = self::getAdditionalFieldName($field->getCode());
        if (isset($this->choiceTypeMaps[$addFieldName])) {
            $options['map'] = $this->choiceTypeMaps[$addFieldName];
            $this->addChoiceFieldMaskTypeField($formMapper, $field, $options);
            return;
        }

        switch ($field->getCode()) {
            case 'homelessFrom':
                $options['years'] = range(date('Y'), date('Y') - 100);
                $formMapper
                    ->add(self::getAdditionalFieldName($field->getCode()), AppHomelessFromDateType::class, $options);
                break;

            default:
                $formMapper
                    ->add(self::getAdditionalFieldName($field->getCode()), $field->getFormFieldType(), $options);
                break;
        }
    }

    /**
     * Заполняем мапы с зависимостями отображаемых полей.
     */
    private function initFieldDependencies(array $enabledFields): void
    {
        $deps = [
            'citizenship' => [
                'Другое' => ['citizenshipOther'],
            ],
            'pensioner' => [
                'Да' => ['pensionReason'],
            ],
            'pensionReason' => [
                'По инвалидности' => ['disabilityGroup'],
            ],
            'student' => [
                'Да' => ['profession'],
            ],
            'liveInHousing' => [
                'Да' => ['housing'],
            ],
            'housing' => [
                'Другое' => ['housingOther'],
            ],
            'registration' => [
                'Да' => ['registrationPlace'],
            ],
            'homelessReason' => [
                'Другие' => ['homelessReasonOther'],
            ],
            'breadwinner' => [
                'Другие' => ['breadwinnerOther'],
            ],
            'breadwinnerMain' => [
                'Другие' => ['breadwinnerOther'],
            ],
            'disease' => [
                'Другие' => ['diseaseOther'],
            ],
        ];

        foreach ($enabledFields as $field) {
            if (isset($deps[$field->getCode()])) {
                $choiceField = self::getAdditionalFieldName($field->getCode());
                foreach ($deps[$field->getCode()] as $valName => $depFields) {
                    $value = $this->getFieldOptionValueId($valName, $field);
                    $addDepFields = [];
                    foreach ($depFields as $depFieldCode) {
                        $depField = self::getAdditionalFieldName($depFieldCode);
                        $addDepFields[] = $depField;
                        $this->dependantFields[$depField][$choiceField][$value] = true;
                    }
                    if (!isset($this->choiceTypeMaps[$choiceField])) {
                        $this->choiceTypeMaps[$choiceField] = [];
                    }
                    $this->choiceTypeMaps[$choiceField][$value] = $addDepFields;
                }
            }
        }
    }

    public function getDependantFields(): array
    {
        return $this->dependantFields;
    }

    /**
     * Зависит ли отображение указанного доп. поля от других полей.
     */
    private function isAdditionalFieldDependant(ClientField $field): bool
    {
        return isset($this->dependantFields[self::getAdditionalFieldName($field->getCode())]);
    }

    private static function getAdditionalFieldName(string $fieldCode): string
    {
        return 'additionalField' . $fieldCode;
    }

    /**
     * Возвращает `true` если поле не было скрыто на форме: если его видимость зависила от значения друогого поля,
     * и это условие выполнилось
     */
    private function fieldCanBeShown(ClientField $field, FormInterface $form): bool
    {
        $fieldName = self::getAdditionalFieldName($field->getCode());
        if (!isset($this->dependantFields[$fieldName])) {
            return true;
        }
        foreach ($this->dependantFields[$fieldName] as $choiceField => $map) {
            $val = $form->get($choiceField)->getData();
            if ($val instanceof ClientFieldOption) {
                return isset($map[$val->getId()]);
            } elseif ($val instanceof ArrayCollection) {
                foreach ($val as $val1) {
                    if ($val1 instanceof ClientFieldOption) {
                        if (isset($map[$val1->getId()])) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Получение идентификатора значения свойства дополнительного поля
     */
    private function getFieldOptionValueId(string $valueName, ClientField $field): ?int
    {
        $fieldOptionRepo = $this->manager->getRepository(ClientFieldOption::class);
        $value = ($fieldOptionRepo->findOneBy(['name' => $valueName, 'field' => $field]));
        return $value?->getId();
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);

        unset($list['create'], $list['list']);

        return $list;
    }
}
