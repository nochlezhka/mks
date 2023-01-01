<?php

namespace App\Admin;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Repository\DocumentRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CertificateAdmin extends BaseAdmin
{
    use UserOwnableTrait;

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected $translationDomain = 'App';

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
        $certificateTypeRepository = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('app.certificate_type.repository');

        $availableCertTypes = $certificateTypeRepository->getAvailableForCertificate($this->getSubject());
        foreach ($availableCertTypes as $availableCertType) {
            $typeOptions['choices'][$availableCertType->getName()] = $availableCertType->getId();
        }

        /** @var CertificateType $travelType */
        $travelType = $certificateTypeRepository->findOneBySyncId(CertificateType::TRAVEL);
        /** @var CertificateType $registrationType */
        $registrationType = $certificateTypeRepository->findOneBySyncId(CertificateType::REGISTRATION);

        $typeOptions['map'] = [
            $travelType->getId() => ['city'],
            $registrationType->getId() => ['document'],
        ];
        $typeOptions['multiple'] = false;
        $typeOptions['label'] = 'Тип';
        $form->add('type', ChoiceFieldMaskType::class, $typeOptions);
        $transformer = $this
            ->getConfigurationPool()
            ->getContainer()->get('app.certificate_type_to_choice_field_mask_type.transformer');
        $form->getFormBuilder()->get('type')->addModelTransformer($transformer);

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
     */
    protected function configureListFields(ListMapper $list): void
    {
        $client = $this->getClient();
        $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('app.certificate_recreator_service')
            ->recreateFor($client);

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
            ->add('_action', null, [
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
