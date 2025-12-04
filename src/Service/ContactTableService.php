<?php

namespace App\Service;

use App\Repository\ContactRepository;

class ContactTableService
{
    public function __construct(
        private readonly ContactRepository $entityRepository,
    ) {
    }

    public function getAllForTableBySearch(?string $search, int $page = 1, int $perPage = 15): array
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('c');

        if (!empty($search)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('c.type', ':search'),
                    $queryBuilder->expr()->like('c.label', ':search'),
                    $queryBuilder->expr()->like('c.value', ':search')
                )
            )->setParameter('search', '%'.$search.'%');
        }

        $queryBuilder->orderBy('c.id', 'DESC');

        $countQb = clone $queryBuilder;
        $countQb->select('COUNT(c.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $queryBuilder->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $data = $queryBuilder->getQuery()->getResult();

        return ['data' => $data, 'total' => $total];
    }
}
