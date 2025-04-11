<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AlbumsCollectionType;
use Doctrine\ORM\EntityManagerInterface;

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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'app_form_album_admin')]
    public function formAlbumsOwnedForAdmin(AlbumRepository $albumRepository, Request $request, EntityManagerInterface $em): Response
    {

        $albums = $albumRepository->findAll();

        $form = $this->createForm(AlbumsCollectionType::class, ['albums' => $albums]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()['albums'];

            foreach ($data as $album) {
                $em->persist($album);
            }
            $em->flush();

            $this->addFlash('success', 'Albums mis à jour');
            return $this->redirectToRoute('app_album');
        }

        return $this->render('form/formAdmin.html.twig', [
            'albums' => $albums,
            'form' => $form,
        ]);
    }
}
