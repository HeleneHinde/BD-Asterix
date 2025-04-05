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
use Psr\Log\LoggerInterface;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class AlbumAuthenticator extends AbstractAuthenticator
{
    private GenericProvider $oidcProvider;
    private UserProviderInterface $userProvider;
    private LoggerInterface $logger;

    public function __construct(GenericProvider $oidcProvider, UserProviderInterface $userProvider, LoggerInterface $logger)
    {
        $this->oidcProvider = $oidcProvider;
        $this->userProvider = $userProvider;
        $this->logger = $logger;
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
            $this->logger->info('Tentative d\'authentification OIDC avec code');

            // Récupère le code d'autorisation de la requête
            $code = $request->query->get('code');

            if (!$code) {
                $this->logger->error('Pas de code d\'autorisation dans la requête');
                throw new AuthenticationException('No authorization code present in the request');
            }

            // Échange le code contre un jeton d'accès
            $accessToken = $this->oidcProvider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $this->logger->info('Token d\'accès obtenu avec succès');

            // Récupère les informations de l'utilisateur
            $resourceOwner = $this->oidcProvider->getResourceOwner($accessToken);
            $userData = $resourceOwner->toArray();

            $this->logger->info('Utilisateur OIDC récupéré: ' . json_encode($userData));

            // Extraire l'identifiant unique de l'utilisateur
            // Pour Synology DSM, vérifier la structure exacte
            $userId = $userData['id'] ?? $userData['sub'] ?? $userData['username'] ?? $userData['email'] ?? null;

            if (!$userId) {
                $this->logger->error('Impossible de déterminer l\'identifiant de l\'utilisateur depuis: ' . json_encode($userData));
                throw new AuthenticationException('No user identifier found in the OIDC response');
            }

            return new SelfValidatingPassport(new UserBadge($userId, function () use ($userId, $userData) {
                $this->logger->info('Chargement de l\'utilisateur avec l\'identifiant: ' . $userId);
                return $this->userProvider->loadUserByIdentifier($userId);
            }));
        } catch (\Exception $e) {
            $this->logger->error('Erreur d\'authentification OIDC: ' . $e->getMessage());
            throw new AuthenticationException('Authentication failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->logger->info('Authentification OIDC réussie pour l\'utilisateur: ' . $token->getUserIdentifier());
        return new RedirectResponse('/'); // Redirige vers la page d'accueil ou une autre page.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->logger->error('Échec de l\'authentification OIDC: ' . $exception->getMessage());
        $request->getSession()->getFlashBag()->add('error', 'Échec de l\'authentification: ' . $exception->getMessage());
        return new RedirectResponse('/login'); // Redirige vers la page de login.
    }
}
