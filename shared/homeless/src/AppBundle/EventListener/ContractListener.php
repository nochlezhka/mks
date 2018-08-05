<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Contract;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ContractListener
{
    /**
     * При создании договора номер устанавливается равным id
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (($entity instanceof Contract) && empty($entity->getNumber())) {
            $entity->setNumber($entity->getId());
            $em = $args->getEntityManager();
            $em->persist($entity);
            $em->flush($entity);
        }
    }
}