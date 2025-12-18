<?php

namespace App\Domain\Contact\CommandHandler;

use App\Domain\Contact\Command\CreateContactCommand;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class CreateContactCommandHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateContactCommand $command): void
    {
        $contact = new Contact();
        $contact->setType($command->getType());
        $contact->setValue($command->getValue());
        $contact->setLabel($command->getLabel());

        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }
}
