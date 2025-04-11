<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use League\OAuth2\Client\Provider\GenericProvider;

class SecurityController extends AbstractController
{
    #[Route('/', name: 'oidc_login')]
    public function login(): Response
    {
        // Rendu simple du template de login
        return $this->render('security/login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('oidc_login');
    }

    #[Route('/oidc-check', name: 'oidc_check')]
    public function oidcCheck(Request $request): Response
    {
        // Simple réponse pour test
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
            ]);

            // Génère l'URL d'autorisation avec les scopes
            $authorizationUrl = $provider->getAuthorizationUrl([
                'scope' => 'openid email profile',
                'response_type' => 'code',
            ]);

            // Enregistre le state dans la session
            $request->getSession()->set('oauth2state', $provider->getState());

            return new RedirectResponse($authorizationUrl);
        } catch (\Exception $e) {
            // Simple redirection en cas d'erreur
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            return $this->redirectToRoute('oidc_login');
        }
    }
}
