<?php

namespace App\Controller\Api;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/contact')]
#[IsGranted('ROLE_ADMIN')]
class ContactApiController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
    ) {
    }

    #[Route('v1/list', name: 'app_api_contact_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $contacts = $this->contactRepository->findAll();
        } catch (\Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to retrieve contact list',
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => 'Contact list retrieved successfully',
            'data' => [
                'contacts' => $contacts,
            ],
        ]);
    }
}
