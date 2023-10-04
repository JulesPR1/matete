<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function getPaginatedPosts(int $page, int $length, $recherche=null){
        $queryBuilder = null;
        if($recherche != null){
            $queryBuilder = $this->CreateQueryBuilder("a")
                ->orderBy("a.id", "DESC")
                ->where("a.nom LIKE :recherche")
                ->orWhere("a.description LIKE :recherche")
                ->setParameter("recherche", "%".$recherche."%")
                ->setFirstResult(($page - 1) * $length)
                ->setMaxResults($length);
        }
        else{
            $queryBuilder = $this->CreateQueryBuilder("a")
                ->orderBy("a.id", "DESC")
                ->setFirstResult(($page - 1) * $length)
                ->setMaxResults($length);
        }
        return $queryBuilder->getQuery()->getResult();   
    }

    public function countAnnonces($recherche=null){
        $queryBuilder = null;

        if ($recherche != null){
            $queryBuilder = $this->CreateQueryBuilder("a")
                ->select("COUNT(a)")
                ->where("a.nom LIKE :recherche")
                ->orWhere("a.description LIKE :recherche")
                ->setParameter("recherche", "%".$recherche."%");
        }
        else{
            $queryBuilder = $this->CreateQueryBuilder("a")
                ->select("COUNT(a)");
        }
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function searchAnnonce($recherche){
        $queryBuilder = $this->CreateQueryBuilder("a")
            ->orderBy("a.id", "DESC")
            ->where("a.nom LIKE :recherche")
            ->orWhere("a.description LIKE :recherche")
            ->setParameter("recherche", "%".$recherche."%");
        return $queryBuilder->getQuery()->getResult();
    }

    public function FindByCat($nom_cat){
        $query = $this->getEntityManager()->createQuery(
            'SELECT a
            FROM App\Entity\Annonce a
            INNER JOIN a.laCategorie c
            WHERE c.nom = :nom_cat'
        )->setParameter('nom_cat', $nom_cat);

        return $query->getResult();
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
