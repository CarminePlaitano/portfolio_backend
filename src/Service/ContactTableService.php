<?php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class ContactTableService
{
    private EntityManagerInterface $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(Contact::class);
    }

    /**
     * @param array $filters ['type'=>..., 'contact_by'=>..., 'q'=>...]
     *
     * @return array ['data'=>Contact[], 'total'=>int]
     */
    public function list(array $filters, int $page = 1, int $perPage = 15): array
    {
        $qb = $this->repo->createQueryBuilder('c');

        // search q (type, value, label)
        if (!empty($filters['q'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.type', ':q'),
                    $qb->expr()->like('c.value', ':q'),
                    $qb->expr()->like('c.label', ':q')
                )
            )->setParameter('q', '%'.$filters['q'].'%');
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('c.type = :type')->setParameter('type', $filters['type']);
        }

        if (!empty($filters['contact_by'])) {
            $qb->andWhere('c.contact_by = :contact_by')->setParameter('contact_by', $filters['contact_by']);
        }

        $qb->orderBy('c.id', 'DESC');

        // clone qb for count
        $countQb = clone $qb;
        $countQb->select('COUNT(c.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // pagination
        $qb->setFirstResult(($page - 1) * $perPage)
           ->setMaxResults($perPage);

        $data = $qb->getQuery()->getResult();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Returns distinct types.
     *
     * @return string[]
     */
    public function getDistinctTypes(): array
    {
        $qb = $this->repo->createQueryBuilder('c')
            ->select('DISTINCT c.type')
            ->orderBy('c.type', 'ASC');

        $rows = $qb->getQuery()->getScalarResult();

        // scalarResult returns array of arrays like ['type' => '...']
        return array_map(fn ($r) => $r['type'], $rows);
    }

    /**
     * Returns distinct contact_by values.
     *
     * @return string[]
     */
    public function getDistinctContactBy(): array
    {
        $qb = $this->repo->createQueryBuilder('c')
            ->select('DISTINCT c.contact_by')
            ->orderBy('c.contact_by', 'ASC');

        $rows = $qb->getQuery()->getScalarResult();

        return array_map(fn ($r) => $r['contact_by'], $rows);
    }
}
