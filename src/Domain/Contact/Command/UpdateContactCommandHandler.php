<?php

namespace App\Domain\Contact\Command;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateContactCommandHandler
{
    private EntityManagerInterface $entityManager;
    private ContactRepository $contactRepository;

    public function __construct(EntityManagerInterface $entityManager, ContactRepository $contactRepository)
    {
        $this->entityManager = $entityManager;
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(UpdateContactCommand $command): void
    {
        $contact = $this->contactRepository->find($command->getId());

        if (!$contact) {
            throw new \Exception('Contact with id ' . $command->getId() . ' not found');
        }

        $contact->setType($command->getType());
        $contact->setValue($command->getValue());
        $contact->setContactBy($command->getContactBy());
        $contact->setLabel($command->getLabel());

        $this->entityManager->flush();
    }
}
