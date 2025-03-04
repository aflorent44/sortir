<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Recherche des utilisateurs par nom, prénom ou pseudo
     *
     * @param string|null $query Le terme de recherche
     * @param int $limit Nombre maximum de résultats à retourner
     * @return User[] Un tableau d'utilisateurs correspondant à la recherche
     */
    public function findByName(?string $query): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.name', 'ASC');

        if (!empty($query)) {
            $qb->andWhere('u.name LIKE :query OR u.firstName LIKE :query OR u.pseudo LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb->getQuery()->getResult();
    }
    public function deleteUser(User $user, EntityManagerInterface $entityManager): void
    {
        $eventsAsParticipant = $entityManager->getRepository(Event::class)->createQueryBuilder('e')
            ->innerJoin('e.participants', 'p')
            ->where('p = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        foreach ($eventsAsParticipant as $event) {
            $event->removeParticipant($user);
        }

        $eventsAsHost = $entityManager->getRepository(Event::class)->findBy(['host' => $user]);

        foreach ($eventsAsHost as $event) {
            $event->setHost(null);
            $event->setStatus(EventStatus::CANCELLED);
            $event->setCancelReason("L'organisteur de la sortie n'est plus inscrit");
        }

        $entityManager->remove($user);
        $entityManager->flush();
    }


}