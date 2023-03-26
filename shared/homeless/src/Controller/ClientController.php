<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\ViewedClient;
use App\Util\UploadedDataStringFile;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends \Sonata\AdminBundle\Controller\CRUDController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws LockException
     * @throws ModelManagerThrowable
     */
    public function preEdit(Request $request, object $object): ?Response
    {
        if (!($object instanceof Client)) {
            return null;
        }
        $base64Photo = $request->request->get('photo');
        if ($base64Photo === null || !str_starts_with($base64Photo, 'data:image')) {
            return null;
        }

        $file = new UploadedDataStringFile($base64Photo, 'client.png');
        $object->setPhoto($file);
        $this->admin->update($object);

        return null;
    }

    public function showAction(Request $request): Response
    {
        $objectId = $request->get($this->admin->getIdParameter());
        if (!\is_string($objectId) && !\is_int($objectId)) {
            return parent::showAction($request);
        }

        $object = $this->admin->getObject($objectId);
        if (!$object instanceof Client) {
            return parent::showAction($request);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return parent::showAction($request);
        }

        // История просмотров анкет
        foreach ($user->getViewedClients() as $viewedClient) {
            if ($object->getId() === $viewedClient->getClient()->getId()) {
                return parent::showAction($request);
            }
        }

        $viewedClient = new ViewedClient();
        $this->entityManager->persist($viewedClient);

        $viewedClient->setClient($object);

        $count = 1;
        foreach ($user->getViewedClients() as $viewedClient) {
            ++$count;

            if ($count >= 30) {
                $this->entityManager->remove($viewedClient);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return parent::showAction($request);
    }
}
