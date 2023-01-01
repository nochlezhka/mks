<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\ViewedClient;
use App\Util\UploadedDataStringFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class ClientController extends \Sonata\AdminBundle\Controller\CRUDController
{
    private ManagerRegistry $managerRegistry;

    /**
     * @throws LockException
     * @throws ModelManagerThrowable
     */
    public function preEdit(Request $request, object $object): ?Response
    {
        if(!($object instanceof Client)) {
            return null;
        }
        $base64Photo = $request->request->get('photo');
        if($base64Photo === null || !str_starts_with($base64Photo, "data:image")) {
            return null;
        }

        $file = new UploadedDataStringFile($base64Photo, "client.png");
        $object->setPhoto($file);
        $this->admin->update($object);
        return null;
    }

    public function showAction(Request $request): Response
    {
        $objectId = $request->get($this->admin->getIdParameter());

        if (is_string($objectId) || is_int($objectId)) {
            $object = $this->admin->getObject($objectId);

            // История просмотров анкет
            if ($object instanceof Client) {
                $user = $this->getUser();

                if ($user instanceof User) {
                    foreach ($user->getViewedClients() as $viewedClient) {
                        if ($object->getId() === $viewedClient->getClient()->getId()) {
                            return parent::showAction($request);
                        }
                    }
                    $em = $this->managerRegistry->getManager();

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
        }

        return parent::showAction($request);
    }

    #[Required]
    public function setManagerRegistry(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }


}