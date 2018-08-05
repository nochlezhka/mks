<?php

namespace AppBundle\Service;

use AppBundle\Entity\Certificate;
use AppBundle\Entity\CertificateType;
use AppBundle\Entity\Client;
use AppBundle\Repository\CertificateTypeRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class CertificateRecreator
 * @package AppBundle\Service
 */
class CertificateRecreator
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CertificateTypeRepository
     */
    private $certificateTypeRepository;

    /**
     * CertificateRecreator constructor.
     * @param EntityManager $entityManager
     * @param CertificateTypeRepository $certificateTypeRepository
     */
    public function __construct(
        EntityManager $entityManager,
        CertificateTypeRepository $certificateTypeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->certificateTypeRepository = $certificateTypeRepository;
    }

    /**
     * Пересоздание всех справок для пользователя
     *
     * @param Client $client
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function recreateFor(Client $client)
    {
        /** @var Certificate $certificate */
        foreach ($client->getCertificates() as $certificate) {
            $this->entityManager->remove($certificate);
        }

        /** @var CertificateType[] $certificateTypes */
        $certificateTypes = $this->certificateTypeRepository->findAll();

        foreach ($certificateTypes as $certificateType){
            $clientCertificate = new Certificate();
            $clientCertificate->setClient($client);
            $clientCertificate->setType($certificateType);
            $this->entityManager->persist($clientCertificate);
        }

        $this->entityManager->flush();
    }
}
