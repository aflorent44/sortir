<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\User;
use App\Enum\EventStatus;
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

    public function findByFilters(
        ?Campus    $campus,
        ?string    $name,
        ?\DateTime $dateMin,
        ?\DateTime $dateMax,
        ?string    $status,
        ?User      $user,
        bool       $isHost,
        bool       $isParticipant,
        bool       $isNotParticipant,
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        dump($status);
        if ($campus) {
            $queryBuilder
                ->join('e.campuses', 'c')
                ->andWhere('c = :campus')
                ->setParameter('campus', $campus);
        }

        if ($name) {
            $queryBuilder->andWhere('e.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        if ($dateMin) {
            $queryBuilder->andWhere('e.beginsAt >= :dateMin')
                ->setParameter('dateMin', $dateMin);
        }

        if ($dateMax) {
            $queryBuilder->andWhere('e.endsAt <= :dateMax')
                ->setParameter('dateMax', $dateMax);
        }

        if ($status || $isHost || $isParticipant || $isNotParticipant) {
            $orX = $queryBuilder->expr()->orX();

            if ($status) {
                $orX->add('e.status = :ended');
                $queryBuilder->setParameter('ended', EventStatus::ENDED);
            }

            if ($isHost) {
                $orX->add('e.host = :user');
            }

            if ($isParticipant) {
                $orX->add(':user MEMBER OF e.participants');
            }

            if($isNotParticipant) {
                $orX->add(':user NOT MEMBER OF e.participants');
            }

            $queryBuilder->andWhere($orX);
        }

        if ($isHost || $isParticipant || $isNotParticipant) {
            $queryBuilder->setParameter('user', $user);
        }

        return $queryBuilder->getQuery()->getResult();
    }


}
