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
    // #[Route(path: '/login', name: 'app_login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();

    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/login.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error' => $error,
    //     ]);
    // }

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
    public function oidcCheck(): void
    {
        // Cette méthode peut rester vide, car le processus d'authentification est géré automatiquement.
    }

    #[Route('/oidc-start', name: 'oidc_start')]
    public function startOidc(Request $request): RedirectResponse
    {
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
            'scope' => 'openid email groups',
        ]);

        // Enregistre le state dans la session pour éviter les attaques CSRF
        $request->getSession()->set('oauth2state', $provider->getState());

        return new RedirectResponse($authorizationUrl);
    }
}
