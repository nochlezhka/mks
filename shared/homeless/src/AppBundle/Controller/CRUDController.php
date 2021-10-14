<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Certificate;
use AppBundle\Entity\Client;
use AppBundle\Entity\Contract;
use AppBundle\Entity\Document;
use AppBundle\Entity\GeneratedDocument;
use AppBundle\Entity\HistoryDownload;
use AppBundle\Entity\ViewedClient;
use AppBundle\Service\DownloadableInterface;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\DocumentType;
use Mnvx\Lowrapper\Format;
use Mnvx\Lowrapper\LowrapperParameters;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CRUDController extends Controller
{
    /**
     * @param $id
     * @return Response
     * @throws \Mnvx\Lowrapper\LowrapperException
     */
    public function downloadAction($id)
    {
        $object = $this->admin->getSubject();

        if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if(!($object instanceof DownloadableInterface)){
            throw new \InvalidArgumentException('AppBundle\Service\DownloadableInterface expected, ' . get_class($object) . ' given');
        }
        $html = '';

        $em = $this->getDoctrine()->getManager();

        switch (get_class($object)){
            case Certificate::class:
                /** @var Certificate| $object */
                if ($this->getRequest()->get('document')) {
                    $document = $em->getRepository(Document::class)->find($this->getRequest()->get('document'));
                    $object->setDocument($document);
                }
                if ($this->getRequest()->get('city')) {
                    $object->setCity($this->getRequest()->get('city'));
                }
                $client = $object->getClient();
                $this->getDoctrine()->getManager()->initializeObject($client);
                $html = $this->get('app.render_service')->renderCertificate($object, $client, $this->getUser());
                $historyDownload = new HistoryDownload();
                $historyDownload->setUser($this->getUser());
                $historyDownload->setClient($client);
                $historyDownload->setCertificateType($object->getType());
                $historyDownload->setDate(new \DateTime());
                $em->persist($historyDownload);
                $em->flush();
                break;

            case GeneratedDocument::class:
                $html = $this->get('app.render_service')->renderGeneratedDocument($object, $this->getUser());
                break;

            case Contract::class;
                $client = $object->getClient();
                $this->getDoctrine()->getManager()->initializeObject($client);
                $html = $this->get('app.render_service')->renderContract($object,$client, $this->getUser());
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
        $filename = $this->get('app.docx_naming_service')->createName($object, $parameters->getOutputFormat());

        return new Response(
            $data,
            200,
            [
                'Content-Type' => 'application/doc',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }

    /**
     * @param null $id
     * @return Response
     */
    public function showAction($id = null)
    {
        $object = $this->admin->getObject($id);

        // История просмотров анкет
        if ($object instanceof Client) {
            $user = $this->getUser();

            if ($user instanceof User) {
                foreach ($user->getViewedClients() as $viewedClient) {
                    if ($object->getId() === $viewedClient->getClient()->getId()) {
                        return parent::showAction($id);
                    }
                }
                $em = $this->getDoctrine()->getManager();

                $viewedClient = new ViewedClient();
                $em->persist($viewedClient);

                $viewedClient->setClient($object);

                $newViewedClients = new ArrayCollection();

                $newViewedClients->add($viewedClient);

                $count = 1;

                foreach ($user->getViewedClients() as $viewedClient) {
                    $count++;

                    if ($count >= 30) {
                        $em->remove($viewedClient);
                    }
                }

                $em->persist($user);
                $em->flush();
            }
        }

        return parent::showAction($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($object)
    {
        if ($this->getRequest()->get('isModal')) {
            return new RedirectResponse($this->getRequest()->get('url'));
        }

        if (null !== $this->getRequest()->get('btn_update_and_show')) {
            $url = $this->admin->generateUrl('show',['id' => $object->getId()]);
            return new RedirectResponse($url);
        }

        return parent::redirectTo($object);
    }
}
