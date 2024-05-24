<?php

namespace App\Repository;

use App\Entity\Research;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Research|null find($id, $lockMode = null, $lockVersion = null)
 * @method Research|null findOneBy(array $criteria, array $orderBy = null)
 * @method Research[]    findAll()
 * @method Research[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Research::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Research $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Research $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function clearHistoryForUser(User $user)
    {
        foreach ($user->getResearch() as $research) {
            $this->remove($research, false);
        }
        $this->_em->flush();
    }

    // /**
    //  * @return Research[] Returns an array of Research objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Research
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
