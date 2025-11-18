<?php

namespace App\Service;

use App\Repository\ContactRepository;

class ContactTableService
{
    public function __construct(
        private readonly ContactRepository $entityRepository,
    ) {
    }

    /**
     * @param array $filters ['contact_by'=>..., 'search'=>...]
     *
     * @return array ['data'=>Contact[], 'total'=>int]
     */
    public function list(array $filters, int $page = 1, int $perPage = 15): array
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('c');

        $search = $filters['search'] ?? null;

        if (!empty($search)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('c.type', ':search'),
                    $queryBuilder->expr()->like('c.label', ':search'),
                    $queryBuilder->expr()->like('c.label', ':search')
                )
            )->setParameter('search', '%'.$search.'%');
        }

        if (!empty($filters['contact_by'])) {
            $queryBuilder->andWhere('c.contact_by = :contact_by')->setParameter('contact_by', $filters['contact_by']);
        }

        $queryBuilder->orderBy('c.id', 'DESC');

        // clone queryBuilder for count
        $countQb = clone $queryBuilder;
        $countQb->select('COUNT(c.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // pagination
        $queryBuilder->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $data = $queryBuilder->getQuery()->getResult();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Returns distinct contact_by values.
     *
     * @return string[]
     */
    public function getDistinctContactBy(): array
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('c')
            ->select('DISTINCT c.contact_by')
            ->orderBy('c.contact_by', 'ASC');

        $rows = $queryBuilder->getQuery()->getScalarResult();

        return array_map(fn ($r) => $r['contact_by'], $rows);
    }
}
