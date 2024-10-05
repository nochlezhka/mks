<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Entity\Document;
use App\Form\DataTransformer\CertificateTypeToChoiceFieldMaskTypeTransformer;
use App\Repository\CertificateTypeRepository;
use App\Repository\DocumentRepository;
use App\Service\CertificateRecreator;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
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
    'code' => 'app.certificate.admin',
    'controller' => CRUDController::class,
    'label' => 'certificates',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Certificate::class,
])]
final class CertificateAdmin extends AbstractAdmin
{
    use AdminTrait;
    use UserOwnableTrait;

    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    ];

    public function __construct(
        private readonly CertificateTypeRepository $certificateTypeRepository,
        private readonly CertificateTypeToChoiceFieldMaskTypeTransformer $transformer,
        private readonly CertificateRecreator $certificateRecreator,
    ) {
        parent::__construct();
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);

        if (\in_array($action, ['show', 'edit', 'delete', 'acl', 'batch'], true)
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['download'] = [
                'template' => '/CRUD/list_certificate_action_download.html.twig',
            ];
        }

        return $list;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('download', $this->getRouterIdParameter().'/download');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->getFormBuilder()->addEventListener(FormEvents::SUBMIT, $this->formSubmit(...));

        // type
        $typeOptions = [];

        $availableCertTypes = $this->certificateTypeRepository->getAvailableForCertificate($this->getSubject());
        foreach ($availableCertTypes as $availableCertType) {
            $typeOptions['choices'][$availableCertType->getName()] = $availableCertType->getId();
        }

        /** @var CertificateType $travelType */
        $travelType = $this->certificateTypeRepository->findOneBy(['syncId' => CertificateType::TRAVEL]);
        /** @var CertificateType $registrationType */
        $registrationType = $this->certificateTypeRepository->findOneBy(['syncId' => CertificateType::REGISTRATION]);

        $typeOptions['map'] = [
            $travelType->getId() => ['city'],
            $registrationType->getId() => ['document'],
        ];
        $typeOptions['multiple'] = false;
        $typeOptions['label'] = 'Тип';

        $form
            ->add('type', ChoiceFieldMaskType::class, $typeOptions)
            ->add('city', null, [
                'label' => 'Город следования *',
                'required' => false,
            ])
            ->add('document', EntityType::class, [
                'label' => 'Основание для выдачи (документ) *',
                'required' => false,
                'class' => Document::class,
                'query_builder' => fn (DocumentRepository $documentRepository): QueryBuilder => $documentRepository->getRegistrationDocumentsQueryBuilderByClient($this->getParent()->getSubject()),
            ])
        ;

        $form->getFormBuilder()->get('type')->addModelTransformer($this->transformer);
    }

    /**
     * Обработка типов справок, требующих указаний значений дополнительных полей при создании
     */
    protected function formSubmit(FormEvent $event): void
    {
        /** @var Certificate $certificate */
        $certificate = $event->getForm()->getViewData();
        switch ($certificate->getType()->getSyncId()) {
            case CertificateType::REGISTRATION:
                if ($certificate->getDocument() === null) {
                    $event->getForm()->get('document')->addError(new FormError('Необходимо указать документ'));
                }
                break;

            case CertificateType::TRAVEL:
                if ($certificate->getCity() === null) {
                    $event->getForm()->get('city')->addError(new FormError('Необходимо указать город'));
                }
                break;

            default:
                break;
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $client = $this->getClient();
        $this->certificateRecreator->recreateFor($client);

        $list
            ->add('type', null, [
                'template' => '/CRUD/list_certificate_type.html.twig',
                'label' => 'Тип',
            ])
            ->add('client.documents', EntityType::class, [
                'label' => 'Доп. поле',
                'template' => '/CRUD/list_certificate_addition.html.twig',
                'class' => Document::class,
                'query_builder' => fn (DocumentRepository $documentRepository): QueryBuilder => $documentRepository->getRegistrationDocumentsQueryBuilderByClient($this->getParent()->getSubject()),
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'delete' => [],
                ],
            ])
        ;
    }
}
