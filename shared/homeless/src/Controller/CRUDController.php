<?php

namespace App\Controller;

use App\Entity\Certificate;
use App\Entity\Contract;
use App\Entity\Document;
use App\Entity\GeneratedDocument;
use App\Entity\HistoryDownload;
use App\Service\DOCXNamingService;
use App\Service\DownloadableInterface;
use App\Service\RenderService;
use Doctrine\Persistence\ManagerRegistry;
use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\DocumentType;
use Mnvx\Lowrapper\Format;
use Mnvx\Lowrapper\LowrapperException;
use Mnvx\Lowrapper\LowrapperParameters;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class CRUDController extends Controller
{
    private RenderService $renderService;

    private ManagerRegistry $managerRegistry;
    private DOCXNamingService $namingService;

    /**
     * @throws LowrapperException
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function downloadAction(Request $request): Response
    {
        $object = $this->admin->getSubject();

        if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $request->get($this->admin->getIdParameter())));
        }

        if(!($object instanceof DownloadableInterface)){
            throw new \InvalidArgumentException('App\Service\DownloadableInterface expected, ' . get_class($object) . ' given');
        }
        $html = '';

        $em = $this->managerRegistry->getManager();

        switch (get_class($object)){
            case Certificate::class:
                /** @var Certificate| $object */
                if ($request->get('document')) {
                    $document = $em->getRepository(Document::class)->find($request->get('document'));
                    $object->setDocument($document);
                }
                if ($request->get('city')) {
                    $object->setCity($request->get('city'));
                }
                $client = $object->getClient();
                $this->managerRegistry->getManager()->initializeObject($client);
                $html = $this->renderService->renderCertificate($object, $client);
                $historyDownload = new HistoryDownload();
                $historyDownload->setUser($this->getUser());
                $historyDownload->setClient($client);
                $historyDownload->setCertificateType($object->getType());
                $historyDownload->setDate(new \DateTime());
                $em->persist($historyDownload);
                $em->flush();
                break;

            case GeneratedDocument::class:
                $html = $this->renderService->renderGeneratedDocument($object);
                break;

            case Contract::class;
                $client = $object->getClient();
                $this->managerRegistry->getManager()->initializeObject($client);
                $html = $this->renderService->renderContract($object,$client, $this->getUser());
                break;
        }

        $converter = new Converter();
        $parameters = (new LowrapperParameters())
            ->setInputData($html)
            ->setDocumentType(DocumentType::WRITER)
            ->setOutputFormat(Format::TEXT_DOCX);
        $data1 = $converter->convert($parameters);
        $converter = new Converter();
        $parameters = (new LowrapperParameters())
            ->setInputData($data1)
            ->setOutputFormat(Format::TEXT_DOCX);
        $data = $converter->convert($parameters);
        $filename = $this->namingService->createName($object, $parameters->getOutputFormat());

        return new Response(
            $data,
            200,
            [
                'Content-Type' => 'application/doc',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }

    #[Required]
    public function setRenderService(RenderService $renderService): void
    {
        $this->renderService = $renderService;
    }

    #[Required]
    public function setManagerRegistry(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Required]
    public function setNamingService(DOCXNamingService $namingService): void
    {
        $this->namingService = $namingService;
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if ($request->get('isModal')) {
            return new RedirectResponse($request->get('url'));
        }

        if (null !== $request->get('btn_update_and_show')) {
            $url = $this->admin->generateUrl('show',['id' => $object->getId()]);
            return new RedirectResponse($url);
        }

        return parent::redirectTo($request, $object);
    }
}
