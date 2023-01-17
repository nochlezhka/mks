<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Form\DataTransformer\CertificateTypeToChoiceFieldMaskTypeTransformer;
use App\Repository\CertificateTypeRepository;
use App\Repository\DocumentRepository;
use App\Service\CertificateRecreator;
use Doctrine\ORM\OptimisticLockException;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Справки',
    'model_class' => Certificate::class,
    'controller'=> CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class CertificateAdmin extends BaseAdmin
{
    use UserOwnableTrait;

    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected string $translationDomain = 'App';
    private CertificateTypeRepository $certificateTypeRepository;
    private CertificateTypeToChoiceFieldMaskTypeTransformer $transformer;
    private CertificateRecreator $certificateRecreator;

    public function __construct(
        CertificateTypeRepository $certificateTypeRepository,
        CertificateTypeToChoiceFieldMaskTypeTransformer $transformer,
        CertificateRecreator $certificateRecreator
    )
    {
        parent::__construct();
        $this->certificateTypeRepository = $certificateTypeRepository;
        $this->transformer = $transformer;
        $this->certificateRecreator = $certificateRecreator;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        /*
         * Обработка типов справок, требующих указаний значений дополнительных полей при создании
         */
        $form->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Certificate $certificate */
            $certificate = $event->getForm()->getViewData();
            switch ($certificate->getType()->getSyncId()) {
                case CertificateType::REGISTRATION:
                    if (null === $certificate->getDocument()) {
                        $event->getForm()->get('document')->addError(new FormError('Необходимо указать документ'));
                    }
                    break;

                case CertificateType::TRAVEL:
                    if (null === $certificate->getCity()) {
                        $event->getForm()->get('city')->addError(new FormError('Необходимо указать город'));
                    }
                    break;

                default:
                    break;
            }
        });

        /* type */
        $typeOptions = [];

        $availableCertTypes = $this->certificateTypeRepository->getAvailableForCertificate($this->getSubject());
        foreach ($availableCertTypes as $availableCertType) {
            $typeOptions['choices'][$availableCertType->getName()] = $availableCertType->getId();
        }

        /** @var CertificateType $travelType */
        $travelType = $this->certificateTypeRepository->findOneBySyncId(CertificateType::TRAVEL);
        /** @var CertificateType $registrationType */
        $registrationType = $this->certificateTypeRepository->findOneBySyncId(CertificateType::REGISTRATION);

        $typeOptions['map'] = [
            $travelType->getId() => ['city'],
            $registrationType->getId() => ['document'],
        ];
        $typeOptions['multiple'] = false;
        $typeOptions['label'] = 'Тип';
        $form->add('type', ChoiceFieldMaskType::class, $typeOptions);
        $form->getFormBuilder()->get('type')->addModelTransformer($this->transformer);

        $form->add('city', null, [
                'label' => 'Город следования *',
                'required' => false,
            ])
            ->add('document', EntityType::class, [
                'label' => 'Основание для выдачи (документ) *',
                'required' => false,
                'class' => 'App\Entity\Document',
                'query_builder' => function (DocumentRepository $documentRepository) {
                    return $documentRepository->getRegistrationDocumentsQueryBuilderByClient($this->getParent()->getSubject());
                },
            ]);
    }

    /**
     * @param ListMapper $list
     * @throws OptimisticLockException
     */
    protected function configureListFields(ListMapper $list): void
    {
        $client = $this->getClient();
        $this->certificateRecreator->recreateFor($client);

        $list
            ->add('type', null, [
                'template' =>'/CRUD/list_certificate_type.html.twig',
                'label' => 'Тип',
            ])
            ->add('client.documents', EntityType::class, [
                'label' => 'Доп. поле',
                'template' =>'/CRUD/list_certificate_addition.html.twig',
                'class' => 'App\Entity\Document',
                'query_builder' => function (DocumentRepository $documentRepository) {
                    return $documentRepository->getRegistrationDocumentsQueryBuilderByClient($this->getParent()->getSubject());
                },
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'delete' => [],
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);

        if (in_array($action, array('show', 'edit', 'delete', 'acl', 'batch'))
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['download'] = array(
                'template' => 'list_certificate_action_download.html.twig',
            );
        }

        return $list;
    }
}
