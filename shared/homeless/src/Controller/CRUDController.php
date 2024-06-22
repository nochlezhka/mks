<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Certificate;
use App\Entity\Contract;
use App\Entity\GeneratedDocument;
use App\Entity\HistoryDownload;
use App\Repository\DocumentRepository;
use App\Service\DOCXNamingService;
use App\Service\DownloadableInterface;
use App\Service\RenderService;
use Doctrine\ORM\EntityManagerInterface;
use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\DocumentType;
use Mnvx\Lowrapper\Format;
use Mnvx\Lowrapper\LowrapperException;
use Mnvx\Lowrapper\LowrapperParameters;
use Sonata\AdminBundle\Controller\CRUDController as SonataCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @method \App\Entity\User getUser()
 */
final class CRUDController extends SonataCRUDController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DocumentRepository $documentRepository,
        private readonly RenderService $renderService,
        private readonly DOCXNamingService $namingService,
    ) {}

    /**
     * @throws LowrapperException
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     */
    public function downloadAction(Request $request): Response
    {
        $object = $this->admin->getSubject();
        if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $request->get($this->admin->getIdParameter())));
        }

        if (!($object instanceof DownloadableInterface)) {
            throw new \InvalidArgumentException(DownloadableInterface::class.' expected, '.$object::class.' given');
        }

        $html = '';
        switch (true) {
            case $object instanceof Certificate:
                if ($request->get('document')) {
                    $document = $this->documentRepository->find($request->get('document'));
                    $object->setDocument($document);
                }
                if ($request->get('city')) {
                    $object->setCity($request->get('city'));
                }
                $client = $object->getClient();
                $this->entityManager->initializeObject($client);
                $html = $this->renderService->renderCertificate($object, $client);

                $historyDownload = new HistoryDownload();
                $historyDownload->setUser($this->getUser());
                $historyDownload->setClient($client);
                $historyDownload->setCertificateType($object->getType());
                $historyDownload->setDate(new \DateTimeImmutable());

                $this->entityManager->persist($historyDownload);
                $this->entityManager->flush();
                break;

            case $object instanceof GeneratedDocument:
                $html = $this->renderService->renderGeneratedDocument($object);
                break;

            case $object instanceof Contract:
                $client = $object->getClient();
                $this->entityManager->initializeObject($client);
                $html = $this->renderService->renderContract($object, $client, $this->getUser());
                break;
        }

        $converter = new Converter();
        $parameters = (new LowrapperParameters())
            ->setInputData($html)
            ->setDocumentType(DocumentType::WRITER)
            ->setOutputFormat(Format::TEXT_DOCX)
        ;
        $data1 = $converter->convert($parameters);
        $converter = new Converter();
        $parameters = (new LowrapperParameters())
            ->setInputData($data1)
            ->setOutputFormat(Format::TEXT_DOCX)
        ;
        $data = $converter->convert($parameters);
        $filename = $this->namingService->createName($object, $parameters->getOutputFormat());

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/doc',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if ($request->get('isModal')) {
            return new RedirectResponse($request->get('url'));
        }

        if ($request->get('btn_update_and_show') !== null) {
            $url = $this->admin->generateUrl('show', ['id' => $object->getId()]);

            return new RedirectResponse($url);
        }

        return parent::redirectTo($request, $object);
    }
}
