<?php

namespace App\Controller\Api\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmailCheckController extends AbstractController
{
    #[Route('/api/admin/check-email', name: 'api_admin_check_email', methods: ['POST'])]
    public function checkEmail(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (null === $email) {
            return new JsonResponse(['exists' => false, 'message' => 'Email not provided'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        return new JsonResponse(['exists' => null !== $user]);
    }
}
