<?php

namespace App\Controller;

use App\Entity\ClientField;
use App\Entity\ContractStatus;
use App\Service\ReportService;
use App\Entity\ShelterRoom;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/app")]
class AppController extends AbstractController
{

    /**
     * Мои клиенты
     */
    #[Route("/my-clients", name: "my_clients")]
    public function myClientsAction(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $inProcessStatus = $doctrine
            ->getRepository(ContractStatus::class)
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        return $this->forward(
            ClientController::class.'::listAction',
            [],
            ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]
        );
    }

    /**
     * Мои бывшие клиенты
     */
    #[Route("/my-ex-clients", name: "my_ex_clients")]
    public function myExClientsAction(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $statuses = $doctrine
            ->getRepository(ContractStatus::class)
            ->findAll();

        $filter['contractStatus']['value'] = [];

        foreach ($statuses as $status) {
            $statusId = $status->getId();

            if ($statusId != ContractStatus::IN_PROCESS) {
                $filter['contractStatus']['value'][] = (string)$statusId;
            }
        }

        return $this->forward(
            ClientController::class.'::list',
            [],
            ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]
        );
    }

    /**
     * Добавить клиента
     */
    #[Route("/add-client", name: "add_client")]
    public function addClientAction(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->forward(CRUDController::class.'::createAction', [], ['_sonata_admin' => 'app.client.admin']);
    }

    /**
     * Оказанные мной услуги
     */
    #[Route("/my-services", name: "my_services")]
    public function myServicesAction(): Response
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
     */
    #[Route("/profile", name: "profile")]
    public function profileAction(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->forward('SonataAdminBundle:CRUD:edit', [], ['_sonata_admin' => 'sonata.user.admin.user', 'id' => $user->getId()]);
    }

    #[Route("/client/list", name: "client_search")]
    public function clientsSearchAction(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_SONATA_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filter = $_GET['filter'] ?? [];

        if (
            (!isset($filter['search']) || empty($filter['search']['value']))
            && (
                !isset($filter['birthDate'])
                && !isset($filter['contractCreatedBy'])
                && !isset($filter['contractStatus'])
            )
        ) {
            $filter['search']['value'] = 'Введите запрос...';
        }

        return $this->forward('SonataAdminBundle:CRUD:list', [], ['_sonata_admin' => 'app.client.admin', 'filter' => $filter]);

    }

    #[Route("/report", name: "report")]
    public function reportAction(ManagerRegistry $doctrine, ReportService $reportService): Response
    {
        /** @var ClientField $fieldHomelessReason */
        /** @var ClientField $fieldDisease */
        /** @var ClientField $fieldHomelessBreadwinner */
        $fieldHomelessReason = $doctrine->getRepository(ClientField::class)->findOneBy(['code' => 'homelessReason']);
        $optionsHomelessReason = $fieldHomelessReason ? $fieldHomelessReason->getOptionsArray() : [];
        $fieldDisease = $doctrine->getRepository(ClientField::class)->findOneBy(['code' => 'disease']);
        $optionsDisease = $fieldDisease ? $fieldDisease->getOptionsArray() : [];
        $fieldHomelessBreadwinner = $doctrine->getRepository(ClientField::class)->findOneBy(['code' => 'breadwinner']);
        $optionsBreadwinner = $fieldHomelessBreadwinner ? $fieldHomelessBreadwinner->getOptionsArray() : [];

        return $this->render('admin/report.html.twig', [
            'users' => $doctrine->getRepository(User::class)->findBy([
                'enabled' => true,
            ]),
            'types' => $reportService->getTypes(),
            'optionsHomelessReason' => $optionsHomelessReason,
            'optionsDisease' => $optionsDisease,
            'optionsBreadwinner' => $optionsBreadwinner,
        ]);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    #[Route("/report-download", name: "reportDownload")]
    public function reportDownloadAction(ReportService $reportService, Request $request): Response
    {
        $type = $request->get('type');
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $type . '.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->sendHeaders();
        $reportService->generate(
            $type,
            $request->get('dateFrom'),
            $request->get('dateTo'),
            $request->get('userId'),

            $request->get('createClientdateFrom'),
            $request->get('createClientFromTo'),
            $request->get('createServicedateFrom'),
            $request->get('createServiceFromTo'),

            $request->get('homelessReason'),
            $request->get('disease'),
            $request->get('breadwinner')
        );
        return $response;
    }

    #[Route("/shelterroom/list", name: "shelter_room")]
    public function shelterRoomAction(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

//        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $roomData = [];
        foreach ($doctrine->getRepository(ShelterRoom::class)->findAll() as $item) {

            $roomData[] = [
                'id' => $item->getId(),
                'number' => $item->getNumber(),
                'maxOccupants' => $item->getMaxOccupants(),
                'currentOccupants' => $item->getcurrentOccupants(),
                'comment' => $item->getComment(),
            ];
        }


        // if ($inProcessStatus instanceof ContractStatus) {
        //     $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        // }

        return $this->render('@App/Admin/shelter_room.html.twig', [
            'rooms' => $roomData
        ]);
    }

    #[Route("/shelterroom/create", name: "shelter_room_add")]
    public function addRoomAction(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createFormBuilder()
            ->setAction('/app/shelterroom/save')
            ->setMethod('GET')
            ->add('number', null, [
                'label' => 'Номер комнаты'
            ])
            ->add('maxOccupants', null, [
                'label' => 'Максимальное кол-во жильцов',
                'required' => false
            ])
            ->add('currentOccupants', null, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false
            ])
            ->getForm();

        $form->handleRequest($request);


        return $this->render('@App/Admin/shelter_room_form.html.twig', [
            'room' => [],
            'form' => $form->createView(),
            'form_title' => 'Добавление комнаты',
            'form_id' => 'saveRoom',
            'form_action' => 'save'
        ]);
    }

    /**
     * Сохранить комнату в БД
     */
    #[Route("/shelterroom/save/", name: "shelter_room_save")]
    public function saveRoomAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();

        $formData = $request->get('form');
        $action = $request->get('action');
        $roomId = $request->get('room_id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if (!empty($formData['_token'])) {
            $em = $doctrine->getManager();


            if ($action == 'edit') {
                if (!empty($roomId)) {
                    $room = $em->getRepository(ShelterRoom::class)->find($roomId);
                } else {
                    throw $this->createNotFoundException(
                        'No product found for id ' . $roomId
                    );
                }
            } else {
                $room = new ShelterRoom();
            }

            $room->setNumber($formData['number']);
            $room->setMaxOccupants(empty($formData['maxOccupants']) ? null : $formData['maxOccupants']);
            $room->setCurrentOccupants(empty($formData['currentOccupants']) ? 0 : $formData['currentOccupants']);
            $room->setComment($formData['comment']);

            $em->persist($room);
            $em->flush();

            return $this->redirect('/app/shelterroom/list');
        } else {
            return $this->redirect('/app/shelterroom/create');
        }

    }


    /**
     * Редактировать комнату
     */
    #[Route("/shelterroom/{id}/edit/", name: "shelter_room_edit")]
    public function editRoomAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        $roomId = $request->attributes->get('id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $room = $doctrine->getRepository(ShelterRoom::class)->find($roomId);

        $form = $this->createFormBuilder()
            ->setAction('/app/shelterroom/save')
            ->setMethod('GET')
            ->add('number', null, [
                'label' => 'Номер комнаты'
            ])
            ->add('maxOccupants', null, [
                'label' => 'Максимальное кол-во жильцов',
                'required' => false
            ])
            ->add('currentOccupants', null, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false
            ])
            ->getForm();


        $formData = [
            'id' => $room->getId(),
            'number' => $room->getNumber(),
            'maxOccupants' => $room->getMaxOccupants(),
            'currentOccupants' => $room->getCurrentOccupants(),
            'comment' => $room->getComment()
        ];


        $form->setData($formData);


        return $this->render('@App/Admin/shelter_room_form.html.twig', [
            'room' => ['id' => $formData['id']],
            'form' => $form->createView(),
            'form_title' => 'Редактирование комнаты',
            'form_id' => 'saveRoom',
            'form_action' => 'edit',
        ]);

    }

    /**
     * Удалить комнату
     * @Route("/shelterroom/{id}/delete/", name="shelter_room_remove")
     */
    public function removeRoomAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();

        $roomId = $request->attributes->get('id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $doctrine->getManager();

        $em->createQuery(
            'DELETE FROM AppBundle:ShelterHistory AS e WHERE e.room = :room_id'
        )->setParameter('room_id', $roomId)->execute();


        $room = $em->getReference('AppBundle:ShelterRoom', $roomId);

        // dump($room); exit;

        $em->remove($room);
        $em->flush();


        $roomData = [];
        foreach ($doctrine->getRepository(ShelterRoom::class)->findAll() as $item) {

            $roomData[] = [
                'id' => $item->getId(),
                'number' => $item->getNumber(),
                'maxOccupants' => $item->getMaxOccupants(),
                'currentOccupants' => $item->getcurrentOccupants(),
                'comment' => $item->getComment(),
            ];
        }

        return $this->render('@App/Admin/shelter_room.html.twig', [
            'rooms' => $roomData
        ]);
    }
}
