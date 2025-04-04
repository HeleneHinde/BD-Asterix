<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use League\OAuth2\Client\Provider\GenericProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class AlbumAuthenticator extends AbstractAuthenticator
{
    private GenericProvider $oidcProvider;
    private UserProviderInterface $userProvider;

    public function __construct(GenericProvider $oidcProvider, UserProviderInterface $userProvider)
    {
        $this->oidcProvider = $oidcProvider;
        $this->userProvider = $userProvider;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/oidc-check' && $request->isMethod('GET');
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->oidcProvider->getAccessToken('authorization_code', [
            'code' => $request->query->get('code'),
        ]);
        $user = $this->oidcProvider->getResourceOwner($accessToken);

        return new SelfValidatingPassport(new UserBadge($user->getId(), function () use ($user) {
            return $this->userProvider->loadUserByIdentifier($user->getId());
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/'); // Redirige vers la page d'accueil ou une autre page.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse('/login'); // Redirige vers la page d'accueil ou une autre page.
    }
}
