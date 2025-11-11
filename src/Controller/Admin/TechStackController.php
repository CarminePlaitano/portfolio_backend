<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
#[Route('/tech-stack')]
class TechStackController extends AbstractController
{
    #[Route('/', name: 'app_tech_stack', methods: ['GET'])]
    public function index()
    {
        return $this->render('pages/tech-stack/index.html.twig');
    }
}
