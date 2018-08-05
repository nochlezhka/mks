<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Certificate;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CertificateListener
{
    /**
     * При создании справки срок ее действия должен задаваться как "Текущая дата + 1 год"
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (($entity instanceof Certificate)) {
            $entity->setDateFrom(new \DateTime());
            $entity->setDateTo((new \DateTime())->modify('+1 year'));
            $em = $args->getEntityManager();
            $em->persist($entity);
            $em->flush($entity);
        }
    }
}
