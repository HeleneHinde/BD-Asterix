<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AlbumController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/album', name: 'app_album')]
    public function listAlbums(AlbumRepository $albumRepository): Response
    {
        // Fetch all albums from the repository
        $albums = $albumRepository->findAll();

        return $this->render('album/index.html.twig', [
            'albums' => $albums,
        ]);
    }
}
