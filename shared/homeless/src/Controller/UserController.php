<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method \App\Entity\User getUser()
 */
final class UserController extends \Sonata\AdminBundle\Controller\CRUDController
{
    public function batchActionDelete(ProxyQueryInterface $query): Response
    {
        $currentUserId = $this->getUser()->getId();
        $selectedUsers = $query->execute();

        foreach ($selectedUsers as $selectedUser) {
            if ($currentUserId === (int) $selectedUser->getId()) {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans('cannot_delete_own_account', [], 'SonataAdminBundle'),
                );

                return $this->redirectToList();
            }
        }

        return parent::batchActionDelete($query);
    }

    public function deleteAction(Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(\sprintf('unable to find the object with id : %s', $id));
        }

        $currentUserId = $this->getUser()->getId();
        if ($currentUserId === (int) $id) {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('cannot_delete_own_account', [], 'SonataAdminBundle'),
            );

            return $this->redirectTo($request, $object);
        }

        return parent::deleteAction($request);
    }
}
