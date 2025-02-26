<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    /**
     * @return Event[] Returns an array of Event objects
     */
//    public function findByCampus(Campus $campus): array
//    {
//        return $this->createQueryBuilder('e')
//            ->join('e.campus.id', 'c')  // Jointure entre 'Event' et 'Campus'
//            ->andWhere('c = :campus')   // On filtre sur le campus spécifique
//            ->setParameter('campus', $campus)
//            ->orderBy('e.beginsAt', 'ASC') // Tri par date de début
//            ->getQuery()
//            ->getResult();
//    }


    public function findByCampus(Campus $campus): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.campuses', 'c')
            ->where('c = :campus')
            ->setParameter('campus', $campus)
            ->getQuery()
            ->getResult();
    }


}
