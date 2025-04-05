<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $request->getPathInfo() === '/oidc-check' && $request->isMethod('GET') && $request->query->has('code');
    }

    public function authenticate(Request $request): Passport
    {
        try {
            // Récupère le code d'autorisation de la requête
            $code = $request->query->get('code');
            
            if (!$code) {
                throw new AuthenticationException('No authorization code present in the request');
            }
            
            // Échange le code contre un jeton d'accès
            $accessToken = $this->oidcProvider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
            
            // Récupère les informations de l'utilisateur
            $resourceOwner = $this->oidcProvider->getResourceOwner($accessToken);
            $userData = $resourceOwner->toArray();
            
            // Extraire l'identifiant unique de l'utilisateur
            $userId = $userData['id'] ?? $userData['sub'] ?? $userData['username'] ?? $userData['email'] ?? null;
            
            if (!$userId) {
                throw new AuthenticationException('No user identifier found in the OIDC response');
            }
            
            return new SelfValidatingPassport(new UserBadge($userId, function () use ($userId) {
                return $this->userProvider->loadUserByIdentifier($userId);
            }));
        } catch (\Exception $e) {
            throw new AuthenticationException('Authentication failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/'); // Redirige vers la page d'accueil ou une autre page.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->getFlashBag()->add('error', 'Échec de l\'authentification: ' . $exception->getMessage());
        return new RedirectResponse('/login'); // Redirige vers la page de login.
    }
}
