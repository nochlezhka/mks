<?php

namespace App\Service;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use App\Entity\Client;
use App\Entity\Contract;
use App\Entity\GeneratedDocument;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RenderService
{
    protected KernelInterface $kernel;
    protected Environment $twig;

    /**
     * RenderService constructor.
     * @param KernelInterface $kernel
     * @param Environment $twig
     */
    public function __construct(
        #[Autowire('@kernel')] KernelInterface $kernel,
        #[Autowire('@twig')] Environment $twig
    ) {
        $this->kernel = $kernel;
        $this->twig = $twig;
    }

    /**
     * Рендеринг справки по шаблону, в зависимости от ее типа
     * @param Certificate $certificate
     * @param Client $client
     * @return null|string
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     */
    public function renderCertificate(Certificate $certificate, Client $client): ?string
    {
        $type = $certificate->getType();

        if (!$type instanceof CertificateType) {
            return null;
        }
        $image = '';
        if (file_exists($client->getPhotoPath())) {
            $image = 'data:image/png;base64,' . base64_encode(file_get_contents($client->getPhotoPath()));
        }
        list($width, $height) = $client->getPhotoSize(300, 350);

        return $this->twig->render('/pdf/certificate/layout.html.twig', [
            'contentHeaderLeft' => empty($type->getContentHeaderLeft()) ? '' : $this->twig->createTemplate($type->getContentHeaderLeft())->render(['certificate' => $certificate]),
            'contentHeaderRight' => empty($type->getContentHeaderRight()) ? '' : $this->twig->createTemplate($type->getContentHeaderRight())->render(['certificate' => $certificate]),
            'contentBodyRight' => empty($type->getContentBodyRight()) ? '' : $this->twig->createTemplate($type->getContentBodyRight())->render(['certificate' => $certificate]),
            'contentFooter' => empty($type->getContentFooter()) ? '' : $this->twig->createTemplate($type->getContentFooter())->render(['certificate' => $certificate]),
            'certificate' => $certificate,
            'rootDir' => $this->kernel->getProjectDir(),
            'webDir' => $this->kernel->getProjectDir() . '/public',
            'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents($this->kernel->getProjectDir() . "/public/" . getenv('BIG_LOGO_PATH'))),
            'image' => $image,
            'height' => $height,
            'width' => $width,
        ]);
    }

    /**
     * Рендеринг построенного документа
     * @param GeneratedDocument $document
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderGeneratedDocument(GeneratedDocument $document): string
    {
        return $this->twig->render('/pdf/generated_document.html.twig', [
            'document' => $document,
            'rootDir' => $this->kernel->getProjectDir(),
            'webDir' => $this->kernel->getProjectDir() . '/public',
            'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents($this->kernel->getProjectDir() . "/public/" . getenv('BIG_LOGO_PATH'))),
        ]);
    }

    /**
     * Рендеринг сервисного плана
     *
     * @param Contract $contract
     * @param Client $client
     * @param User $user
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderContract(Contract $contract, Client $client, User $user): string
    {
        $image = '';
        if (file_exists($client->getPhotoPath())) {
            $image = 'data:image/png;base64,' . base64_encode(file_get_contents($client->getPhotoPath()));
        }
        list($width, $height) = $client->getPhotoSize(300, 350);
        return $this->twig->render('/pdf/contract.html.twig', [
            'contract' => $contract,
            'client' => $client,
            'user' => $user,
            'specialty' => ($user->getPositionText() ?: ($user->getPosition() ? $user->getPosition()->getName() : 'Специалист по социальной работе')),
            'webDir' => $this->kernel->getProjectDir() . '/public',
            'image' => $image,
            'height' => $height,
            'width' => $width,
        ]);
    }
}
