<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
#[Route('/about-me')]
class AboutMeController extends AbstractController
{
    #[Route('/', name: 'app_about_me', methods: ['GET'])]
    public function index()
    {
        return $this->render('pages/about-me/index.html.twig');
    }
}
