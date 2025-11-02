<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/{id}', name: 'app_user_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail($id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (!$user) throw $this->createNotFoundException('User not found');

        return $this->render('user/detail.html.twig', [
            'user' => $user
        ]);
    }
}
