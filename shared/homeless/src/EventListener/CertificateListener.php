<?php

namespace App\EventListener;

use App\Entity\Certificate;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Certificate::class)]
class CertificateListener
{
    /**
     * При создании справки срок ее действия должен задаваться как "Текущая дата + 1 год"
     */
    public function postPersist(Certificate $certificate, LifecycleEventArgs $args): void
    {
        $certificate->setDateFrom(new DateTime());
        $certificate->setDateTo((new DateTime())->modify('+1 year'));
        $em = $args->getObjectManager();
        $em->persist($certificate);
        $em->flush($certificate);
    }
}
