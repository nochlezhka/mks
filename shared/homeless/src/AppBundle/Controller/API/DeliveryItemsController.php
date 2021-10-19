<?php


namespace AppBundle\Controller\API;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;

class DeliveryItemsController extends FOSRestController {

    /**
     * @Route("/delivery_items")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDeliveryItemsAction()
    {
        $res =  $this->getDoctrine()->getEntityManager()
            ->createQuery('SELECT i.id, i.name, i.category, i.limitDays AS limit_days
            FROM AppBundle\Entity\DeliveryItem i ORDER BY i.name')
            ->getResult();

        $view = $this->view($res, 200);
        return $this->handleView($view);
    }

}
