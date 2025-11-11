<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
#[Route('/reviews')]
class ReviewsController extends AbstractController
{
    #[Route('/', name: 'app_reviews', methods: ['GET'])]
    public function index()
    {
        return $this->render('pages/reviews/index.html.twig');
    }
}
