<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private $logger;
    public function __construct(UrlGeneratorInterface $urlGenerator, LoggerInterface $logger)
    {
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response('invalid credentials', status: 401);
    }

    public function authenticate(Request $request): Passport
    {

        $authenticationData = json_decode($request->getContent(), true);
        $email = $authenticationData['email'];
        $password = $authenticationData['password'];
        $csrf_token = $authenticationData['csrf_token'];

         return  new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [

            ],
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // For example:
        return new Response($token->getUserIdentifier());
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function supports(Request $request): bool
    {

        return  $request->getPathInfo() === '/login'&&  $request->isMethod('POST');
    }


}
