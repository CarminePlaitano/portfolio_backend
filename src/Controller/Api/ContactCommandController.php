<?php

namespace App\Controller\Api;

use App\Domain\Contact\Command\CreateContactCommand;
use App\Domain\Contact\Command\UpdateContactCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/command/contact')]
#[IsGranted('ROLE_ADMIN')]
class ContactCommandController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'command.bus')]
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/v1/create', name: 'app_api_contact_command_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $command = new CreateContactCommand(
            $data['type'],
            $data['value'],
            $data['contact_by'],
            $data['label'] ?? null
        );

        $this->handle($command);

        return $this->json([
            'success' => true,
            'message' => 'Contact created successfully',
        ]);
    }

    #[Route('/v1/update/{id}', name: 'app_api_contact_command_update', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->toArray();

        $command = new UpdateContactCommand(
            $id,
            $data['type'],
            $data['value'],
            $data['contact_by'],
            $data['label'] ?? null
        );

        try {
            $this->handle($command);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return $this->json([
            'success' => true,
            'message' => 'Contact updated successfully',
        ]);
    }
}
