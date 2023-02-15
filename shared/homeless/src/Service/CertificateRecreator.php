<?php

namespace App\Service;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Entity\Client;
use App\Repository\CertificateTypeRepository;
use Doctrine\Persistence\ManagerRegistry;

class CertificateRecreator
{
    private ManagerRegistry $managerRegistry;
    private CertificateTypeRepository $certificateTypeRepository;

    public function __construct(
        ManagerRegistry $managerRegistry,
        CertificateTypeRepository $certificateTypeRepository
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->certificateTypeRepository = $certificateTypeRepository;
    }

    /**
     * Пересоздание всех справок для пользователя
     *
     */
    public function recreateFor(?Client $client): void
    {
        $em = $this->managerRegistry->getManager();
        /** @var Certificate $certificate */
        foreach ($client->getCertificates() as $certificate) {
            $em->remove($certificate);
        }

        /** @var CertificateType[] $certificateTypes */
        $certificateTypes = $this->certificateTypeRepository->findAll();

        foreach ($certificateTypes as $certificateType){
            $clientCertificate = new Certificate();
            $clientCertificate->setClient($client);
            $clientCertificate->setType($certificateType);
            $em->persist($clientCertificate);
        }

        $em->flush();
    }
}
