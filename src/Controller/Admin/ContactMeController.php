<?php

namespace App\Controller\Admin;

use App\Service\ContactTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ContactMeController extends AbstractController
{
    public function __construct(
        private readonly ContactTableService $contactTableService,
    ) {
    }

    #[Route('/contact-me', name: 'app_contact_me_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = (int) $request->query->get('perPage', 15);

        $filters = [
            'type' => $request->query->get('type'),
            'contact_by' => $request->query->get('contact_by'),
            'search' => $request->query->get('search'),
        ];

        $result = $this->contactTableService->list($filters, $page, $itemsPerPage);

        return $this->render('pages/contact-me/index.html.twig', [
            'contacts' => $result['data'],
            'totalContacts' => $result['total'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => (int) ceil($result['total'] / $itemsPerPage),
            'distinctContactByValues' => $this->contactTableService->getDistinctContactBy(),
        ]);
    }

    #[Route('/contact-me/list', name: 'app_contact_me_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        // read 'perPage' to match Stimulus
        $perPage = (int) $request->query->get('perPage', 15);

        $filters = [
            'type' => $request->query->get('type'),
            'contact_by' => $request->query->get('contact_by'),
            // use 'q' as search key (Stimulus sends 'q')
            'q' => $request->query->get('q'),
        ];

        $result = $this->contactTableService->list($filters, $page, $perPage);

        $contactsAsArray = array_map(function ($contactEntity) {
            return [
                'id' => $contactEntity->getId(),
                'type' => $contactEntity->getType(),
                'value' => $contactEntity->getValue(),
                'contactBy' => $contactEntity->getContactBy(),
                'label' => $contactEntity->getLabel(),
            ];
        }, $result['data']);

        // Return keys expected by your Stimulus controller: data, page, perPage, total
        return $this->json([
            'data' => $contactsAsArray,
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }
}
