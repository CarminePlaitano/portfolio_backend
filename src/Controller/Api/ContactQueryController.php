<?php

namespace App\Controller\Api;

use App\Domain\Contact\Query\GetAllContactsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/query/contact')]

class ContactQueryController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'query.bus')]
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/v1/list', name: 'app_api_contact_query_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $query = new GetAllContactsQuery();
        $contacts = $this->handle($query);

        return $this->json([
            'success' => true,
            'message' => 'Contact list retrieved successfully',
            'data' => [
                'contacts' => $contacts,
            ],
        ]);
    }
}
