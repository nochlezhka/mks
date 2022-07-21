<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClientField;
use AppBundle\Entity\ContractStatus;
use AppBundle\Entity\ShelterRoom;
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

    /**
     * @Route("/shelterroom/list",name="shelter_room")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shelterRoomAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

//        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $roomData = [];
        foreach ($this->getDoctrine()->getRepository('AppBundle:ShelterRoom')->findAll() as $item) {

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


    /**
     * Добавить комнату
     * @Route("/shelterroom/create", name="shelter_room_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRoomAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $room = new ShelterRoom();

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
     * @Route("/shelterroom/save/", name="shelter_room_save")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveRoomAction(Request $request)
    {
        $user = $this->getUser();

        $formData = $request->get('form');
        $action = $request->get('action');
        $roomId = $request->get('room_id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if (!empty($formData['_token'])) {
            $em = $this->getDoctrine()->getManager();


            if ($action == 'edit') {
                if (!empty($roomId)) {
                    $room = $em->getRepository('AppBundle:ShelterRoom')->find($roomId);
                } else {
                    throw $this->createNotFoundException(
                        'No product found for id '.$roomId
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
     * @Route("/shelterroom/{id}/edit/", name="shelter_room_edit")
     * @param Request id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRoomAction(Request $request)
    {
        $user = $this->getUser();
        $roomId = $request->attributes->get('id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('AppBundle:ShelterRoom')->find($roomId);

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
     * @param Request id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeRoomAction(Request $request)
    {
        $user = $this->getUser();

        $roomId = $request->attributes->get('id');

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'DELETE FROM AppBundle:ShelterHistory AS e WHERE e.room = :room_id'
        )->setParameter('room_id', $roomId)->execute();


        $room = $em->getReference('AppBundle:ShelterRoom', $roomId);

        // dump($room); exit;

        $em->remove($room);
        $em->flush();


        $roomData = [];
        foreach ($this->getDoctrine()->getRepository('AppBundle:ShelterRoom')->findAll() as $item) {

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
