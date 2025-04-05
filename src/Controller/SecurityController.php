<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use League\OAuth2\Client\Provider\GenericProvider;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/login', name: 'oidc_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/oidc-check', name: 'oidc_check')]
    public function oidcCheck(Request $request): Response
    {
        $error = $request->query->get('error');
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        $sessionState = $request->getSession()->get('oauth2state');

        // Vérifier si une erreur est renvoyée par le serveur d'autorisation
        if ($error) {
            $this->logger->error('Erreur OIDC: ' . $error);
            $this->addFlash('error', 'Échec de l\'authentification: ' . $error);
            return $this->redirectToRoute('oidc_login');
        }

        // Vérifier que le code est présent
        if (!$code) {
            $this->logger->error('Pas de code d\'autorisation reçu');
            $this->addFlash('error', 'Pas de code d\'autorisation reçu');
            return $this->redirectToRoute('oidc_login');
        }

        // Vérifier que le state est valide (protection CSRF)
        if (!$state || $state !== $sessionState) {
            $this->logger->error('State invalide, possible attaque CSRF');
            $request->getSession()->remove('oauth2state');
            $this->addFlash('error', 'État de session invalide, veuillez réessayer');
            return $this->redirectToRoute('oidc_login');
        }

        // L'authenticator prendra le relais à partir d'ici
        return new Response('Authentification en cours...');
    }

    #[Route('/oidc-start', name: 'oidc_start')]
    public function startOidc(Request $request): RedirectResponse
    {
        try {
            $provider = new GenericProvider([
                'clientId' => $_ENV['OIDC_CLIENT_ID'],
                'clientSecret' => $_ENV['OIDC_CLIENT_SECRET'],
                'redirectUri' => $_ENV['OIDC_REDIRECT_URI'],
                'urlAuthorize' => $_ENV['OIDC_URL_AUTHORIZE'],
                'urlAccessToken' => $_ENV['OIDC_URL_ACCESS_TOKEN'],
                'urlResourceOwnerDetails' => $_ENV['OIDC_URL_RESOURCE_OWNER'],
                'responseResourceOwnerId' => 'id',  // Utilise 'id' comme champ d'identifiant
            ]);

            // Génère l'URL d'autorisation avec les scopes
            $authorizationUrl = $provider->getAuthorizationUrl([
                'scope' => 'openid email profile',
                'response_type' => 'code',
            ]);

            // Enregistre le state dans la session pour éviter les attaques CSRF
            $request->getSession()->set('oauth2state', $provider->getState());

            $this->logger->info('Redirection vers le serveur d\'autorisation: ' . $authorizationUrl);

            return new RedirectResponse($authorizationUrl);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la redirection vers le serveur d\'autorisation: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur de configuration OIDC: ' . $e->getMessage());
            return $this->redirectToRoute('oidc_login');
        }
    }
}
