<?php

namespace App\Domain\Contact\Query;

use App\Repository\ContactRepository;

class GetAllContactsQueryHandler
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(GetAllContactsQuery $query): array
    {
        return $this->contactRepository->findAll();
    }
}
