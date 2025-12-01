<?php

namespace App\Domain\Contact\Command;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeleteContactCommandHandler
{
    private EntityManagerInterface $entityManager;
    private ContactRepository $contactRepository;

    public function __construct(EntityManagerInterface $entityManager, ContactRepository $contactRepository)
    {
        $this->entityManager = $entityManager;
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(DeleteContactCommand $command): void
    {
        $contact = $this->contactRepository->find($command->getId());

        if (!$contact) {
            throw new \Exception('Contact with id ' . $command->getId() . ' not found');
        }

        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }
}
