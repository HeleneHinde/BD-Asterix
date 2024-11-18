<?php

namespace App\Controller;

use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TypeController extends AbstractController
{
    #[Route('/type', name: 'list_type')]
    public function list(TypeRepository $typeRepository): Response
    {

        $types = $typeRepository->findBy([]);

        return $this->render('type/list.html.twig', [
            'types' => $types,
        ]);
    }
}
