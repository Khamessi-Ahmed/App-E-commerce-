<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Achat>
 *
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }

    // public function OccurrencesLivres(): array
    // {
    //     $query = $this->createQueryBuilder("c")
    //         ->select('c.livre, COUNT(c.livre) as livreCount')
    //         // ->from('App\Entity\Commande', 'c')
    //         ->groupBy('c.livre')
    //         ->getQuery();
    //
    //     return $query->getResult();
    // }

    public function OccurrencesLivres(): array
    {
        return $this->createQueryBuilder("c")
                      ->select('l.titre, COUNT(l) as total')
                      ->join('c.livre', 'l')
                      ->groupBy('l.id')
                      ->getQuery()
                      ->getResult();

    }

    public function OccurrencesCategorie(): array
    {
        return $this->createQueryBuilder('d')
            ->select('c.id, c.libelle AS nom, SUM(l) as total')
            ->join('d.livre', 'l')
            ->join('l.categorie', 'c')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Achat[] Returns an array of Achat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Achat
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
