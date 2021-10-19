<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientFieldValue;
use AppBundle\Entity\Delivery;
use AppBundle\View\API\ClientResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class ClientsController extends FOSRestController
{

    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_APP_CLIENT_ADMIN_LIST')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @View(serializerGroups={"client"})
     */
    public function getClientsSearchAction()
    {
        // TODO: reuse https://github.com/homelessru/mks/blob/master/shared/homeless/src/AppBundle/Controller/AppController.php#L136 ?
        $searchVal = $this->getRequest()->query->get('v'); // TODO: deprecated in Symfony 3+, inject
        $clients = $this->getDoctrine()->getRepository('AppBundle:Client')
                        ->search($searchVal);

        $view = $this->view($clients, 200);
        return $this->handleView($view);
    }

    /**
     * @param $id
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_APP_CLIENT_ADMIN_VIEW')")
     *
     * @View(serializerGroups={"client"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getClientAction($id)
    {
        $client= $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($id);

        if (is_null($client)) {
            throw new NotFoundHttpException('Client not found');
        }

        $cr = new ClientResponse($client);

        $extraFields = explode(',', $this->getRequest()->query->get('fetch'));

        if (in_array('diseases', $extraFields)) {
            $diseases = $this->getDoctrine()
                ->getRepository(ClientFieldValue::class)
                ->findOneByClientAndFieldCode($client, 'disease');

            $cr->setDiseases($diseases);
        }

        $view = $this->view($cr, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/clients/{clientID}/deliveries")
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_APP_SERVICE_ADMIN_ALL')")
     *
     * TODO: better api docs
     * @param $clientID
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getClientDeliveriesAction($clientID)
    {
        $this->checkClientExists($clientID);

        $items = $this
               ->getDoctrine()
               ->getEntityManager()
               ->createQueryBuilder()
               ->select('MAX(d.deliveredAt) AS delivered_at, IDENTITY(d.deliveryItem) AS delivery_item_id')
               ->from('AppBundle\Entity\Delivery', 'd')
               ->andWhere('IDENTITY(d.client) = :cid')
               ->groupBy('d.deliveryItem')
               ->setParameter('cid', $clientID)
               ->getQuery()
               ->getArrayResult();

        $view = $this->view($items, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/clients/{clientID}/services")
     * @Rest\QueryParam(name="types", description="array of service type ids")
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_APP_SERVICE_ADMIN_ALL')")
     *
     * TODO: better api docs
     * @param $clientID
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getClientServicesAction($clientID)
    {
        $req = $this->getRequest();
        $types = $req->query->get('types');

        $this->checkClientExists($clientID);

        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();

        $services = $qb
                  ->select('s.comment, s.amount, IDENTITY(s.type) AS type, s.createdAt AS created_at, s.updatedAt AS updated_at')
                  ->from('AppBundle\Entity\Service', 's')
                  ->andWhere('IDENTITY(s.client) = :cid')
                  ->andWhere($qb->expr()->in('IDENTITY(s.type)', ':types'))
                  ->setParameter('cid', $clientID)
                  ->setParameter('types', $types)
                  ->orderBy('s.createdAt', 'DESC')
                  ->getQuery()
                  ->getArrayResult();

        $view = $this->view($services, 200);
        return $this->handleView($view);
    }

    private function checkClientExists($clientID)
    {
        /** @var Doctrine\ORM\Query $query */
        $query = $this->getDoctrine()->getEntityManager()
               ->createQuery('SELECT 1 FROM AppBundle\Entity\Client c WHERE c.id = :cid')
               ->setParameter('cid', $clientID)
               ->setMaxResults(1);

        if (count($query->getResult()) === 0) {
            throw new NotFoundHttpException('Client not found');
        }
    }

    /**
     * @Route("/clients/{clientID}/deliveries")
     * TODO: better api docs
     * @param $clientID
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_APP_SERVICE_ADMIN_ALL')")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @View(serializerGroups={"client"})
     */
    public function postClientDeliveriesAction($clientID)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($this->getRequest()->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        $client = $em->getRepository('AppBundle:Client')->find($clientID);
        if (!$client instanceof Client) {
            throw new NotFoundHttpException('Client not found');
        }

        $items = $em->getRepository('AppBundle:DeliveryItem')->findById($data['item_ids']);

        foreach ($items as $item) {
            $delivery = (new Delivery())
                      ->setClient($client)
                      ->setDeliveryItem($item)
                      ->setDeliveredAt(new \DateTime());

            $em->persist($delivery);
        }

        $em->flush();
    }

}
