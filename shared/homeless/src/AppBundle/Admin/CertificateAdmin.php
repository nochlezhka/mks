<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Certificate;
use AppBundle\Entity\CertificateType;
use AppBundle\Repository\DocumentRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Route\RouteCollection;
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

    protected $translationDomain = 'AppBundle';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /*
         * Обработка типов справок, требующих указаний значений дополнительных полей при создании
         */
        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
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
        $formMapper->add('type', ChoiceFieldMaskType::class, $typeOptions);
        $transformer = $this
            ->getConfigurationPool()
            ->getContainer()->get('app.certificate_type_to_choice_field_mask_type.transformer');
        $formMapper->getFormBuilder()->get('type')->addModelTransformer($transformer);

        $formMapper->add('city', null, [
                'label' => 'Город следования *',
                'required' => false,
            ])
            ->add('document', 'entity', [
                'label' => 'Основание для выдачи (документ) *',
                'required' => false,
                'class' => 'AppBundle\Entity\Document',
                'query_builder' => function (DocumentRepository $documentRepository) {
                    return $documentRepository->getRegistrationDocumentsQueryBuilderByClient($this->getParent()->getSubject());
                },
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $client = $this->getClient();
        $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('app.certificate_recreator_service')
            ->recreateFor($client);

        $listMapper
            ->add('type', null, [
                'template' =>'/CRUD/list_certificate_type.html.twig',
                'label' => 'Тип',
            ])
            ->add('client.documents', 'entity', [
                'label' => 'Доп. поле',
                'template' =>'/CRUD/list_certificate_addition.html.twig',
                'class' => 'AppBundle\Entity\Document',
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
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

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

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $lastQuery = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Certificate')
            ->createQueryBuilder('ct')
            ->setMaxResults(1)
            ->orderBy('ct.id', 'DESC')
            ->getQuery()
            ->getResult();

        if (!empty($lastQuery[0])) {
            $lastNumber = $lastQuery[0]->getNumber();
            $instance->setNumber($lastNumber);
        }

        return $instance;
    }
}
