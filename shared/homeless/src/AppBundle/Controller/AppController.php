<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClientField;
use AppBundle\Entity\ContractStatus;
use Application\Sonata\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/app")
 */
class AppController extends Controller
{

    /**
     * Мои клиенты
     * @Route("/my-clients", name="my_clients")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myClientsAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $inProcessStatus = $this
            ->getDoctrine()
            ->getEntityManager()
            ->getRepository('AppBundle:ContractStatus')
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        return $this->forward('SonataAdminBundle:CRUD:list', [], ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]);
    }

    /**
     * Мои бывшие клиенты
     * @Route("/my-ex-clients", name="my_ex_clients")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myExClientsAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $statuses = $this
            ->getDoctrine()
            ->getEntityManager()
            ->getRepository('AppBundle:ContractStatus')
            ->findAll();

        $filter['contractStatus']['value'] = [];

        foreach ($statuses as $status) {
            $statusId = $status->getId();

            if ($statusId != ContractStatus::IN_PROCESS) {
                $filter['contractStatus']['value'][] = (string)$statusId;
            }
        }

        return $this->forward('SonataAdminBundle:CRUD:list', [], ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]);
    }

    /**
     * Добавить клиента
     * @Route("/add-client", name="add_client")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addClientAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->forward('SonataAdminBundle:CRUD:create', [], ['_sonata_admin' => 'app.client.admin']);
    }

    /**
     * Оказанные мной услуги
     * @Route("/my-services", name="my_services")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myServicesAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = ['createdBy' => ['value' => $user->getId()]];

        return $this->forward('SonataAdminBundle:CRUD:list', [], ['_sonata_admin' => 'app.service.admin', 'filter' => $filter]);
    }

    /**
     * Профиль
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->forward('SonataAdminBundle:CRUD:edit', [], ['_sonata_admin' => 'sonata.user.admin.user', 'id' => $user->getId()]);
    }

    /**
     * @Route("/client/list",name="client_search")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientsSearchAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = isset($_GET['filter'] )?  $_GET['filter'] : [];

        if (
            (!isset($filter['search']) || empty($filter['search']['value']))
            && (
                !isset($filter['birthDate'] )
                && !isset($filter['contractCreatedBy'])
                && !isset($filter['contractStatus'])
            )
        ) {
            $filter['search']['value'] = 'Введите запрос...';
        }

        return $this->forward('SonataAdminBundle:CRUD:list', [], ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]);

    }

    /**
     * @Route("/report",name="report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportAction(Request $request)
    {
        $report = $this->get('app.report_service');

        /** @var ClientField $fieldHomelessReason */
        /** @var ClientField $fieldDisease */
        /** @var ClientField $fieldHomelessBreadwinner */
        $fieldHomelessReason = $this->getDoctrine()->getRepository(ClientField::class)->findOneBy(['code' => 'homelessReason']);
        $optionsHomelessReason = $fieldHomelessReason ? $fieldHomelessReason->getOptionsArray() : [];
        $fieldDisease = $this->getDoctrine()->getRepository(ClientField::class)->findOneBy(['code' => 'disease']);
        $optionsDisease = $fieldDisease ? $fieldDisease->getOptionsArray() : [];
        $fieldHomelessBreadwinner = $this->getDoctrine()->getRepository(ClientField::class)->findOneBy(['code' => 'breadwinner']);
        $optionsBreadwinner = $fieldHomelessBreadwinner ? $fieldHomelessBreadwinner->getOptionsArray() : [];

        return $this->render('@App/Admin/report.html.twig', [
            'users' => $this->getDoctrine()->getEntityManager()->getRepository('ApplicationSonataUserBundle:User')->findBy([
                'locked' => false,
            ]),
            'types' => $report->getTypes(),
            'optionsHomelessReason' => $optionsHomelessReason,
            'optionsDisease' => $optionsDisease,
            'optionsBreadwinner' => $optionsBreadwinner,
        ]);
    }

    /**
     * @Route("/report-download",name="reportDownload")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportDownloadAction(Request $request)
    {
        $report = $this->get('app.report_service');
        $report->generate(
            $request->get('type'),
            $request->get('dateFrom', null),
            $request->get('dateTo', null),
            $request->get('userId', null),

            $request->get('createClientdateFrom', null),
            $request->get('createClientFromTo', null),
            $request->get('createServicedateFrom', null),
            $request->get('createServiceFromTo', null),

            $request->get('homelessReason', null),
            $request->get('disease', null),
            $request->get('breadwinner', null)
        );
    }
}
