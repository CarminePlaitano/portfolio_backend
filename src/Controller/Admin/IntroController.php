<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IntroController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/intro', name: 'app_intro', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/intro/index.html.twig');
    }
}
