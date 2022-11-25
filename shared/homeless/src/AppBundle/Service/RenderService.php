<?php

namespace AppBundle\Service;

use AppBundle\Entity\Certificate;
use AppBundle\Entity\CertificateType;
use AppBundle\Entity\Client;
use AppBundle\Entity\Contract;
use Application\Sonata\UserBundle\Entity\User;
use AppBundle\Entity\GeneratedDocument;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class RenderService
{
    protected EngineInterface $templating;
    protected KernelInterface $kernel;
    protected Environment $twig;

    /**
     * RenderService constructor.
     * @param EngineInterface $templating
     * @param KernelInterface $kernel
     * @param Environment $twig
     */
    public function __construct(
        EngineInterface $templating,
        KernelInterface $kernel,
        Environment $twig
    ) {
        $this->templating = $templating;
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

        return $this->templating->render('/pdf/certificate/layout.html.twig', [
            'contentHeaderLeft' => empty($type->getContentHeaderLeft()) ? '' : $this->twig->createTemplate($type->getContentHeaderLeft())->render(['certificate' => $certificate]),
            'contentHeaderRight' => empty($type->getContentHeaderRight()) ? '' : $this->twig->createTemplate($type->getContentHeaderRight())->render(['certificate' => $certificate]),
            'contentBodyRight' => empty($type->getContentBodyRight()) ? '' : $this->twig->createTemplate($type->getContentBodyRight())->render(['certificate' => $certificate]),
            'contentFooter' => empty($type->getContentFooter()) ? '' : $this->twig->createTemplate($type->getContentFooter())->render(['certificate' => $certificate]),
            'certificate' => $certificate,
            'rootDir' => $this->kernel->getRootDir(),
            'webDir' => $this->kernel->getRootDir() . '/../web',
            'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents(dirname($this->kernel->getRootDir()) . "/web/render/logo_big.png")),
            'image' => $image,
            'height' => $height,
            'width' => $width,
        ]);
    }

    /**
     * Рендеринг построенного документа
     * @param GeneratedDocument $document
     * @return string
     *
     */
    public function renderGeneratedDocument(GeneratedDocument $document): string
    {
        return $this->templating->render('/pdf/generated_document.html.twig', [
            'document' => $document,
            'rootDir' => $this->kernel->getRootDir(),
            'webDir' => $this->kernel->getRootDir() . '/../web',
            'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents(dirname($this->kernel->getRootDir()) . "/web/render/logo_big.png")),
        ]);
    }

    /**
     * Рендеринг сервисного плана
     *
     * @param Contract $contract
     * @param Client $client
     * @param User $user
     * @return string
     */
    public function renderContract(Contract $contract, Client $client, User $user): string
    {
        $image = '';
        if (file_exists($client->getPhotoPath())) {
            $image = 'data:image/png;base64,' . base64_encode(file_get_contents($client->getPhotoPath()));
        }
        list($width, $height) = $client->getPhotoSize(300, 350);
        return $this->templating->render('/pdf/contract.html.twig', [
            'contract' => $contract,
            'client' => $client,
            'user' => $user,
            'specialty' => ($user->getPositionText() ?: ($user->getPosition() ? $user->getPosition()->getName() : 'Специалист по социальной работе')),
            'webDir' => $this->kernel->getRootDir() . '/../web',
            'image' => $image,
            'height' => $height,
            'width' => $width,
        ]);
    }
}
