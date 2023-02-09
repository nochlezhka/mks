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
        $branch = $this->getRequest()->query->get('b',null);
        $where = $branch ? 'LEFT JOIN i.branches b WHERE b.id = :b' : '';

        $sql_q = 'SELECT i.id, i.name, i.category, i.limitDays AS limit_days
            FROM AppBundle\Entity\DeliveryItem i ' . $where . ' ORDER BY i.name';

        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery($sql_q);
        if ($branch) {
            $query->setParameter(':b', $branch);
        }

        $res = $query->getResult();

        $view = $this->view($res, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/branches")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBranchesAction()
    {
        $res =  $this->getDoctrine()->getEntityManager()
            ->createQuery('SELECT i.id, i.name
            FROM AppBundle\Entity\Branch i ORDER BY i.name')
            ->getResult();

        $view = $this->view($res, 200);
        return $this->handleView($view);
    }

}
