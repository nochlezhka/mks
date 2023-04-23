<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Service;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Entity\Client;
use App\Repository\CertificateTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class CertificateRecreator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CertificateTypeRepository $certificateTypeRepository,
    ) {}

    /**
     * Пересоздание всех справок для пользователя
     */
    public function recreateFor(Client $client): void
    {
        /** @var Certificate $certificate */
        foreach ($client->getCertificates() as $certificate) {
            $this->entityManager->remove($certificate);
        }

        /** @var array<CertificateType> $certificateTypes */
        $certificateTypes = $this->certificateTypeRepository->findAll();

        foreach ($certificateTypes as $certificateType) {
            $clientCertificate = new Certificate();
            $clientCertificate->setClient($client);
            $clientCertificate->setType($certificateType);
            $this->entityManager->persist($clientCertificate);
        }

        $this->entityManager->flush();
    }
}
