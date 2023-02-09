<?php


namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;


class ApiAuthenticator extends AbstractFormLoginAuthenticator
{

    protected function getLoginUrl()
    {}

    protected function getDefaultSuccessRedirectUrl()
    {}

    public function getCredentials(Request $request)
    {}

    public function getUser($credentials, UserProviderInterface $userProvider)
    {}

    public function checkCredentials($credentials, UserInterface $user)
    {}

    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse(['code' => Response::HTTP_UNAUTHORIZED, 'error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

}
