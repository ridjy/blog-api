<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PagerFanta\Adapater\DoctrineORMAdapter;
use PagerFanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function add(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search($term, $order = 'asc', $limit = 20, $offset = 1)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.fullname', $order)
        ;
        
        if ($term) {
            $qb
                ->where('a.fullname LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }
        
        return $this->paginate($qb, $limit, $offset);
    }

    protected function paginate(QueryBuilder $qb, $limit = 20, $offset = 1)
    {
        if (0 == $limit || 0 == $offset) {
            throw new \LogicException('$limit & $offstet must be greater than 0.');
        }
        
        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $currentPage = ceil(($offset + 1) / $limit);
        $pager->setCurrentPage($currentPage);
        $pager->setMaxPerPage((int) $limit);
        
        return $pager;
    }

}
