<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\ClientController;
use App\Entity\Client;
use App\Entity\ClientField;
use App\Entity\ClientFieldOption;
use App\Entity\ClientFieldValue;
use App\Entity\ContractStatus;
use App\Entity\MenuItem;
use App\Entity\ShelterHistory;
use App\Entity\User;
use App\Form\DataTransformer\AdditionalFieldToArrayTransformer;
use App\Form\Type\AppHomelessFromDateType;
use App\Form\Type\AppPhotoType;
use App\Repository\ClientFieldRepository;
use App\Repository\ClientFieldValueRepository;
use App\Security\User\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.client.admin',
    'controller' => ClientController::class,
    'label' => 'Клиенты',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Client::class,
])]
final class ClientAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'contracts.dateFrom',
    ];

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
     */
    private array $choiceTypeMaps = [];

    public function __construct(
        private readonly AdditionalFieldToArrayTransformer $additionalFieldToArrayTransformer,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ClientFieldRepository $clientFieldRepository,
        private readonly ClientFieldValueRepository $clientFieldValueRepository,
    ) {
        parent::__construct();
    }

    /**
     * @throws \ReflectionException
     */
    public function configureShowFields(ShowMapper $show): void
    {
        $show->with('photo', ['class' => 'col-md-4 photo-block']);
        $show
            ->add('photo', null, [
                'label' => 'Фото',
                'template' => '/admin/fields/client_photo_show.html.twig',
            ])
        ;
        $show->end();

        $show->with('base_info', ['class' => 'col-md-4']);
        $show
            ->add('fullname', null, [
                'label' => 'ФИО',
            ])
            ->add('id', TextType::class, [
                'label' => 'ID',
            ])
            ->add('gender', null, [
                'label' => 'Пол',
                'template' => '/admin/fields/client_gender.html.twig',
            ])
            ->add('birthDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата рождения',
            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
            ])
        ;
        if ($this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS)) {
            $show->add('isHomeless', null, [
                'label' => 'Статус',
                'template' => '/admin/fields/client_notIsHomeless.html.twig',
            ]);
        }
        $show->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
            'label' => 'Дата добавления',
        ]);
        if ($this->isMenuItemEnabled(MenuItem::CODE_SHELTER_HISTORY)) {
            $show->add('shelterHistories', EntityType::class, [
                'label' => 'Проживание в приюте',
                'template' => '/admin/fields/client_shelter_histories_show.html.twig',
            ]);
        }
        $show->end();

        if ($this->authorizationChecker->isGranted(Role::APP_SERVICE_ADMIN_ALL)) {
            $show->with('last_services', ['class' => 'col-md-4']);
            $show
                ->add('services', FieldDescriptionInterface::TYPE_ARRAY, [
                    'label' => ' ',
                    'template' => '/admin/fields/client_services_show.html.twig',
                ])
            ;
            $show->end();
        }

        if ($this->authorizationChecker->isGranted(Role::APP_NOTE_ADMIN_ALL)) {
            $show->with('last_notes');
            $show
                ->add('notes', FieldDescriptionInterface::TYPE_ARRAY, [
                    'label' => ' ',
                    'template' => '/admin/fields/client_notes_show.html.twig',
                ])
            ;
            $show->end();
        }

        if (!$this->authorizationChecker->isGranted(Role::APP_CLIENT_ADMIN_ALL)) {
            return;
        }

        $show->with('additional_info', [
            'class' => 'col-md-12',
            'box_class' => 'box box-primary box-client-field-all',
            'type' => 'additional-info',
            'subtype' => 'main-block-start',
        ]);

        $showMapperAdditionalInfo = [[
            'with' => ['   ', [
                'class' => 'col-md-4',
                'box_class' => 'box-client-field',
                'type' => 'additional-info',
                'subtype' => 'item',
            ]],
        ]];

        // Дополнительные поля клиента
        if ($this->hasSubject()) {
            $fieldValues = $this->clientFieldValueRepository->findByClient($this->getSubject());

            $blankTabName = '    ';
            foreach ($fieldValues as $key => $fieldValue) {
                if ($key !== 0) {
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
                $fieldCode = $field->getCode();

                $options = [
                    'label' => $field->getName(),
                    'accessor' => static fn (Client $client): mixed => $client->getAdditionalFieldValue($fieldCode),
                ];

                switch ($field->getType()) {
                    case ClientField::TYPE_OPTION:
                        $options['choices'] = $field->getOptionsArray();
                        if ($field->isMultiple()) {
                            $options['multiple'] = true;
                            $options['template'] = '/admin/fields/show_array.html.twig';
                        }
                        break;

                    case ClientField::TYPE_FILE:
                        $options['template'] = '/admin/fields/client_file_show.html.twig';
                        break;

                    case ClientField::TYPE_TEXT:
                        $options['template'] = '/admin/fields/client_text_show.html.twig';
                        break;
                }

                if ($fieldCode === 'homelessFrom') {
                    $options['pattern'] = 'MMM y';
                }

                $showMapperAdditionalInfo[\count($showMapperAdditionalInfo) - 1]['add'] = [
                    self::getAdditionalFieldName($fieldCode),
                    $field->getShowFieldType(),
                    $options,
                ];
            }
        }

        $showMapperAdditionalInfoSort = [];
        foreach ($showMapperAdditionalInfo as $key => $item) {
            $showMapperAdditionalInfoSort[$key % ceil(\count($showMapperAdditionalInfo) / 3)][(int) ($key / ceil(\count($showMapperAdditionalInfo) / 3))] = $item;
        }

        foreach ($showMapperAdditionalInfoSort as $showMapperAdditionalInfoSortItems) {
            foreach ($showMapperAdditionalInfoSortItems as $item) {
                if (isset($item['add'])) {
                    $show->add(...$item['add']);
                }
            }
        }
        $show->end();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function prePersist(object $object): void
    {
        $this->processFieldValues($object);
    }

    /**
     * @param mixed $object
     *
     * @throws NonUniqueResultException
     */
    public function preUpdate(object $object): void
    {
        $this->processFieldValues($object, true);
    }

    /**
     * Создает нужные и удаляет ненужные объекты значений дополнительных полей
     *
     * @throws NonUniqueResultException
     */
    public function processFieldValues(object $object, bool $isUpdate = false): void
    {
        if (!($object instanceof Client)) {
            return;
        }

        if ($isUpdate) {
            foreach ($object->getAdditionalFieldValuesToRemove() as $code) {
                $fieldValue = $this->clientFieldValueRepository->findOneByClientAndFieldCode($object, $code);

                if ($fieldValue instanceof ClientFieldValue) {
                    $this->entityManager->remove($fieldValue);
                }
            }

            unset($code, $fieldValue);
        }

        foreach ($object->getAdditionalFieldValues() as $code => $value) {
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

            $this->entityManager->persist($fieldValue);
        }
    }

    public function getContractCreatedByFilter(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias.'.contracts', 'c1');
        $queryBuilder->leftJoin('c1.createdBy', 'u1');
        $queryBuilder->andWhere('u1.id = :userId');
        $queryBuilder->setParameter('userId', $data->getValue());

        return true;
    }

    public function getContractStatusFilter(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        if (!\in_array('c1', $queryBuilder->getAllAliases(), true)) {
            $queryBuilder->leftJoin($alias.'.contracts', 'c1');
        }

        $queryBuilder->leftJoin('c1.status', 's1');
        $queryBuilder->andWhere('s1.id IN (:statuses)');
        $queryBuilder->setParameter('statuses', $data->getValue()->toArray());

        return true;
    }

    public function getClientSearchFilter(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias.'.notes', 'n');
        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("({$alias}.lastname LIKE :word{$key} OR {$alias}.firstname LIKE :word{$key} OR {$alias}.middlename LIKE :word{$key} OR {$alias}.id LIKE :word{$key} OR {$alias}.birthDate LIKE :word{$key} OR n.text LIKE :word{$key})");
            $queryBuilder->orderBy("{$alias}.lastname, {$alias}.firstname, {$alias}.middlename", 'ASC');
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function getClientSearchLastName(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("({$alias}.lastname LIKE :word{$key})");
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function getClientSearchFirstName(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("({$alias}.firstname LIKE :word{$key})");
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function getClientSearchMiddleName(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("({$alias}.middlename LIKE :word{$key})");
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function getClientSearchNote(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias.'.notes', 'sn');

        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("(sn.text LIKE :word{$key})");
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function getClientSearchContract(ProxyQueryInterface $queryBuilder, string $alias, string $_, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryBuilder->leftJoin($alias.'.contracts', 'con');
        $queryBuilder->leftJoin('con.items', 'i');

        $words = array_filter(array_map('trim', explode(' ', $data->getValue())));

        foreach ($words as $key => $word) {
            $queryBuilder->andWhere("(con.comment LIKE :word{$key} OR i.comment LIKE :word{$key})");
            $queryBuilder->setParameter("word{$key}", "%{$word}%");
        }

        return true;
    }

    public function isMenuItemEnabled($menuItemCode): bool
    {
        return $this->menuItemRepository->isEnableCode($menuItemCode);
    }

    public function isMenuItemEnabledShelterHistory($client): bool
    {
        return (bool) $this->entityManager->getRepository(ShelterHistory::class)->findOneBy(['client' => $client]);
    }

    public function getDependantFields(): array
    {
        return $this->dependantFields;
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);
        unset($list['create'], $list['list']);

        return $list;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->getFormBuilder()->addEventListener(FormEvents::SUBMIT, $this->formSubmit(...));

        $form->with('base_info');
        $form
            ->add('photo', AppPhotoType::class, [
                'label' => 'Фото',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
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
            ->add('birthDate', DatePickerType::class, [
                'datepicker_options' => [
                    'defaultDate' => (new \DateTimeImmutable('-50 year'))->format('Y-m-d'),
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата рождения',
                'required' => true,
                'input' => 'datetime_immutable',
            ])
            ->add('birthPlace', null, [
                'label' => 'Место рождения',
                'required' => true,
            ])
        ;
        if ($this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS)) {
            $form
                ->add('notIsHomeless', CheckboxType::class, [
                    'label' => 'Не бездомный',
                    'label_attr' => ['class' => 'changeSelectinsData'],
                    'required' => false,
                    'getter' => static fn (Client $client): bool => !$client->isHomeless(),
                    'setter' => static fn (Client $client, bool $value): Client => $client->setIsHomeless(!$value),
                ])
            ;
        }
        $form->end();

        // дополнительные поля клиента
        $form->with('additional_info');

        $fields = $this->clientFieldRepository->findByEnabledAll();
        $this->initFieldDependencies($fields);

        foreach ($fields as $field) {
            $options = [
                'label' => $field->getName(),
                'required' => $field->isRequired(),
                'attr' => ['class' => ($field->isMandatoryForHomeless() ? 'mandatory-for-homeless' : '').' '.(!$field->isEnabled() && $field->isEnabledForHomeless() ? 'enabled-for-homeless' : '')],
                'getter' => static fn (Client $client): mixed => $client->getAdditionalFieldValue($field->getCode()),
                'setter' => static fn (Client $client, $value): null => $client->setAdditionalFieldValue($field->getCode(), $value),
            ];
            // если скрываемое поле раньше не было обязательным, разрешаем ему оставаться пустым
            // (это также поддержано в валидации в обработчике `FormEvents::SUBMIT`)
            if ($this->canAdditionalFieldRemainEmpty($field)) {
                $options['required'] = false;
            }

            switch ($field->getType()) {
                case ClientField::TYPE_OPTION:
                    $options['class'] = ClientFieldOption::class;

                    $options['query_builder'] = static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('o')
                        ->where('o.field = :field')
                        ->addOrderBy('o.sort', 'ASC')
                        ->setParameter('field', $field)
                    ;

                    if ($field->isMultiple()) {
                        $options['multiple'] = true;
                    }
                    // Когда у селекта выставлен `required`, по-умолчанию выбирается первый элемент из списка.
                    // Это может быть нежелательно для скрываемых полей - мы делаем их необязательными, если они скрыты.
                    // Чтобы избежать незаметной отправки непустого значения, указываем `placeholder`.
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
                    'mapped' => false,
                    'required' => true,
                ])
            ;
        }

        $form->end();
    }

    protected function formSubmit(FormEvent $event): void
    {
        $client = $event->getForm()->getData();
        if (!$client instanceof Client) {
            return;
        }

        // Проверяем заполненность обязательных доп. полей
        $clientsFields = $this->clientFieldRepository->findAll();
        $statusHomelessEnabled = $this->isMenuItemEnabled(MenuItem::CODE_STATUS_HOMELESS);
        foreach ($clientsFields as $clientsField) {
            $isRequired = $clientsField->isRequired()
                || $statusHomelessEnabled && $clientsField->isMandatoryForHomeless() && $client->isHomeless();
            if (!$isRequired) {
                continue;
            }

            $additionalFieldName = self::getAdditionalFieldName($clientsField->getCode());
            $newValue = $event->getForm()->get($additionalFieldName)->getData();
            if (self::isAdditionalFieldValueEmpty($newValue)
                // не требуем заполненности скрытых полей
                && $this->fieldCanBeShown($clientsField, $event->getForm())
                // если редактируется старый клиент, и значение в БД уже пустое - прощаем
                && !$this->canAdditionalFieldRemainEmpty($clientsField)
            ) {
                $event->getForm()->get($additionalFieldName)->addError(new FormError('Поле обязательное для заполнения'));
            }
        }

        foreach ($client->getFieldValues() as $fieldValue) {
            if (!$fieldValue instanceof ClientFieldValue) {
                continue;
            }

            $field = $fieldValue->getField();
            if (!$field instanceof ClientField
                || $field->getType() !== ClientField::TYPE_OPTION
            ) {
                continue;
            }

            $options = $event->getForm()->getNormData()->getAdditionalFieldValues();

            if (!isset($options[$field->getCode()])) {
                continue;
            }

            foreach ($options[$field->getCode()] as $value) {
                if (!$value instanceof ClientFieldOption) {
                    continue;
                }

                if ($value->isNotSingle() && \count($options[$field->getCode()]) === 1) {
                    $event->getForm()->get(self::getAdditionalFieldName($field->getCode()))->addError(new FormError("'{$value->getName()}' не может быть единственным ответом"));
                }
            }
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('lastContractDuration', null, [
                'template' => '/admin/fields/client_contract_duration_list.html.twig',
                'label' => ' ',
                'virtual_field' => true,
            ])
            ->addIdentifier('id', 'number', [
                'route' => ['name' => 'show'],
            ])
            ->addIdentifier('lastname', null, [
                'label' => 'ФИО',
                'template' => '/admin/fields/client_fullname_list.html.twig',
                'route' => ['name' => 'show'],
            ])
            ->add('birthDate', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Дата рождения',
            ])
            ->add('contracts', null, [
                'template' => '/admin/fields/client_contract_list.html.twig',
                'label' => 'Договор',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлен',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('search', CallbackFilter::class, [
                'label' => 'Поиск',
                'callback' => [$this, 'getClientSearchFilter'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('lastName', CallbackFilter::class, [
                'label' => 'Фамилия',
                'callback' => [$this, 'getClientSearchLastName'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('firstName', CallbackFilter::class, [
                'label' => 'Имя',
                'callback' => [$this, 'getClientSearchFirstName'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('middleName', CallbackFilter::class, [
                'label' => 'Отчество',
                'callback' => [$this, 'getClientSearchMiddleName'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('note', CallbackFilter::class, [
                'label' => 'Примечание',
                'callback' => [$this, 'getClientSearchNote'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('contract', CallbackFilter::class, [
                'label' => 'Сервисный план',
                'callback' => [$this, 'getClientSearchContract'],
                'field_type' => TextType::class,
                'global_search' => true,
                'advanced_filter' => false,
            ])
            ->add('birthDate', DateRangeFilter::class, [
                'label' => 'Дата рождения',
                'advanced_filter' => false,
            ], [
                'field_type' => DateRangePickerType::class,
                'field_options' => [
                    'field_options_start' => [
                        'label' => 'От',
                        'format' => 'dd.MM.yyyy',
                    ],
                    'field_options_end' => [
                        'label' => 'До',
                        'format' => 'dd.MM.yyyy',
                    ],
                ],
            ])
            ->add('contractCreatedBy', CallbackFilter::class, [
                'label' => 'Кем добавлен договор',
                'callback' => [$this, 'getContractCreatedByFilter'],
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => User::class,
                    'choice_label' => 'fullname',
                    'multiple' => false,
                    'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('u')
                        ->select('u')
                        ->orderBy('CONCAT(u.lastname, u.firstname, u.middlename)', 'ASC'),
                ],
                'advanced_filter' => false,
            ])
            ->add('contractStatus', CallbackFilter::class, [
                'label' => 'Статус договора',
                'callback' => [$this, 'getContractStatusFilter'],
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => ContractStatus::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC'),
                ],
                'advanced_filter' => false,
            ])
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    protected function configureTabMenu(MenuItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['show', 'edit'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        if ($this->authorizationChecker->isGranted(Role::APP_DOCUMENT_ADMIN_ALL)) {
            $menu->addChild('Документы', [
                'uri' => $admin->generateUrl('app.document.admin.list', [
                    'id' => $id,
                ]),
            ]);
        }

        if ($this->authorizationChecker->isGranted(Role::APP_DOCUMENT_FILE_ADMIN_ALL)) {
            $menu->addChild('Файлы', [
                'uri' => $admin->generateUrl('app.document_file.admin.list', [
                    'id' => $id,
                ]),
            ]);
        }

        if ($this->authorizationChecker->isGranted(Role::APP_CONTRACT_ADMIN_ALL)) {
            $menu->addChild('Сервисные планы', [
                'uri' => $admin->generateUrl('app.contract.admin.list', [
                    'id' => $id,
                ]),
            ]);
        }

        if ($this->authorizationChecker->isGranted(Role::APP_SHELTER_HISTORY_ADMIN_ALL)
            && $this->isMenuItemEnabled(MenuItem::CODE_SHELTER_HISTORY)
            && $this->isMenuItemEnabledShelterHistory($id)
        ) {
            $menu->addChild('Проживание в приюте', [
                'uri' => $admin->generateUrl('app.shelter_history.admin.list', [
                    'id' => $id,
                ]),
            ]);
        }

        if ($this->authorizationChecker->isGranted(Role::APP_RESIDENT_FORM_RESPONSE_ADMIN_ALL)) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_QUESTIONNAIRE_LIVING) && $this->isMenuItemEnabledShelterHistory($id)) {
                $menu->addChild('Анкета', [
                    'uri' => $admin->generateUrl('app.resident_form_response.admin.list', [
                        'id' => $id,
                    ]),
                ]);
            }
        }

        if ($this->authorizationChecker->isGranted(Role::APP_CERTIFICATE_ADMIN_ALL)) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_CERTIFICATE)) {
                $menu->addChild('Выдать справку', [
                    'uri' => $admin->generateUrl('app.certificate.admin.list', [
                        'id' => $id,
                    ]),
                ]);
            }
        }

        if ($this->authorizationChecker->isGranted(Role::APP_GENERATED_DOCUMENT_ADMIN_ALL)) {
            if ($this->isMenuItemEnabled(MenuItem::CODE_GENERATED_DOCUMENT)) {
                $menu->addChild('Построить документ', [
                    'uri' => $admin->generateUrl('app.generated_document.admin.list', [
                        'id' => $id,
                    ])],
                );
            }
        }

        if (!$this->authorizationChecker->isGranted(Role::APP_NOTICE_ADMIN_ALL)) {
            return;
        }

        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        $noticesCount = $this->noticeRepository->getUnviewedCount($this->getSubject(), $user);

        $menu->addChild('Напоминания'.($noticesCount > 0 ? " ({$noticesCount})" : ''), [
            'uri' => $admin->generateUrl('app.notice.admin.list', [
                'id' => $id,
                'filter' => [
                    'date' => [
                        'value' => [
                            'end' => date('d.m.Y'),
                        ],
                    ],
                    'viewed' => [
                        'value' => 2,
                    ],
                ],
            ])],
        );
    }

    private static function isAdditionalFieldValueEmpty(mixed $val): bool
    {
        return $val instanceof Collection ? $val->count() === 0 : !$val;
    }

    private static function getAdditionalFieldName(string $fieldCode): string
    {
        return 'additionalField'.$fieldCode;
    }

    /**
     * Можно ли не заполнять доп. поле.
     * Разрешаем не заполнять обязательные поля, если редактируется старый клиент, и в БД значение уже пустое.
     */
    private function canAdditionalFieldRemainEmpty(ClientField $field): bool
    {
        if ($this->isAdditionalFieldDependant($field) && $this->getSubject()->getId()) {
            $curVal = $this->getSubject()->getAdditionalFieldValue($field->getCode());

            return self::isAdditionalFieldValueEmpty($curVal);
        }

        return false;
    }

    /**
     * Возвращает массив для использования как значение опции choices в конструкторе поля формы
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
     */
    private function getProcessedOptionsForChoiceFieldMaskTypeField(array $options, ClientField $field): array
    {
        unset($options['class'], $options['query_builder']);

        $options['choices'] = $this->getChoices($field);

        return $options;
    }

    /**
     * Добавляет поле типа ChoiceFieldMaskType к $formMapper
     */
    private function addChoiceFieldMaskTypeField(FormMapper $formMapper, ClientField $field, array $options): void
    {
        $options = $this->getProcessedOptionsForChoiceFieldMaskTypeField($options, $field);
        $formMapper->add(self::getAdditionalFieldName($field->getCode()), ChoiceFieldMaskType::class, $options);
        $formMapper->getFormBuilder()
            ->get(self::getAdditionalFieldName($field->getCode()))
            ->addModelTransformer($this->additionalFieldToArrayTransformer)
        ;
    }

    /**
     * Реализация зависимости отображения доп. полей формы в зависимости друг от друга
     */
    private function addClientField(FormMapper $form, ClientField $field, array $options): void
    {
        $additionalFieldName = self::getAdditionalFieldName($field->getCode());
        if (isset($this->choiceTypeMaps[$additionalFieldName])) {
            $options['map'] = $this->choiceTypeMaps[$additionalFieldName];
            $this->addChoiceFieldMaskTypeField($form, $field, $options);

            return;
        }

        switch ($field->getCode()) {
            case 'homelessFrom':
                $options['years'] = range(date('Y'), date('Y') - 100);
                $options['widget'] = 'choice';
                $form->add($additionalFieldName, AppHomelessFromDateType::class, $options);
                break;

            default:
                if ($field->getType() === ClientField::TYPE_DATETIME) {
                    $options['input'] = 'datetime_immutable';
                    $options['widget'] = 'choice';
                }
                $form->add($additionalFieldName, $field->getFormFieldType(), $options);
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
            if (!isset($deps[$field->getCode()])) {
                continue;
            }

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

    /**
     * Зависит ли отображение указанного доп. поля от других полей.
     */
    private function isAdditionalFieldDependant(ClientField $field): bool
    {
        return isset($this->dependantFields[self::getAdditionalFieldName($field->getCode())]);
    }

    /**
     * Возвращает `true` если поле не было скрыто на форме: если его видимость зависела от значения другого поля,
     * и это условие выполнилось
     */
    private function fieldCanBeShown(ClientField $field, FormInterface $form): bool
    {
        $fieldName = self::getAdditionalFieldName($field->getCode());
        if (!isset($this->dependantFields[$fieldName])) {
            return true;
        }

        foreach ($this->dependantFields[$fieldName] as $choiceField => $map) {
            $modelData = $form->get($choiceField)->getData();
            if ($modelData instanceof ClientFieldOption) {
                return isset($map[$modelData->getId()]);
            }
            if (!($modelData instanceof ArrayCollection)) {
                continue;
            }

            foreach ($modelData as $element) {
                if ($element instanceof ClientFieldOption) {
                    if (isset($map[$element->getId()])) {
                        return true;
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
        $fieldOptionRepo = $this->entityManager->getRepository(ClientFieldOption::class);
        $value = $fieldOptionRepo->findOneBy([
            'name' => $valueName,
            'field' => $field,
        ]);

        return $value?->getId();
    }
}
