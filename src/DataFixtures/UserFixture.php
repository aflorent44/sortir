<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        //création admin
        $admin = new User();
        $admin->setEmail('admin@campus-eni.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName($faker->lastName);
        $admin->setFirstName($faker->firstName);
        $admin->setPhoneNumber('0601020304');
        $admin->setActive(true);
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin123');
        $admin->setPassword($hashedPassword);

        //association campus aléatoire
        $randomCampus = $this->getReference('campus_'.rand(1, 4),  Campus::class);
        $admin->setCampus($randomCampus);

        $manager->persist($admin);


        //créer plusieurs users test
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@campus-eni.fr");
            $user->setRoles(['ROLE_USER']);
            $user->setName($faker->lastName);
            $user->setFirstName($faker->firstName);
            $user->setPhoneNumber('06' . $faker->numerify('########'));
            $user->setActive($faker->boolean(80)); // 80% de profil actif);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password123'
            );
            $user->setPassword($hashedPassword);

            $randomCampus = $this->getReference('campus_' .rand(1, 4),  Campus::class);
            $user->setCampus($randomCampus);

            $manager->persist($user);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CampusFixtures::class,
        ];
    }
}
