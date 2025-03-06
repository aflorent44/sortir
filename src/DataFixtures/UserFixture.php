<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixture extends Fixture implements DependentFixtureInterface
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
        $admin->setPseudo('admin_test');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName($faker->lastName);
        $admin->setFirstName($faker->firstName);
        $admin->setPhoneNumber('0601020304');
        $admin->setIsActive(true);
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin123');
        $admin->setPassword($hashedPassword);

        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $admin->setCampus($randomCampus);

        $manager->persist($admin);

        $this->addReference('admin', $admin);

        //créer plusieurs users test
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@campus-eni.fr");
            $user->setPseudo("user$i");
            $user->setRoles(['ROLE_USER']);
            $user->setName($faker->lastName);
            $user->setFirstName($faker->firstName);
            $user->setPhoneNumber('06' . $faker->numerify('########'));
            $user->setIsActive($faker->boolean(90)); // 80% de profil actif);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password123'
            );
            $user->setPassword($hashedPassword);

            $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
            $user->setCampus($randomCampus);
            $this->addReference('user_' . $i, $user);
            $manager->persist($user);
        }

        $yoann = new User();
        $yoann->setEmail('yoann.battu2024@campus-eni.fr');
        $yoann->setPseudo('yoyo');
        $yoann->setRoles(['ROLE_USER']);
        $yoann->setName('Battu');
        $yoann->setFirstName('Yoann');
        $yoann->setPhoneNumber('06' . $faker->numerify('########'));
        $yoann->setIsActive(true);
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $yoann,
            'password123');
        $yoann->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $yoann->setCampus($randomCampus);
        $manager->persist($yoann);
        $this->addReference('yoann_battu', $yoann);

        $amelie = new User();
        $amelie->setEmail('amelie.caillet2024@campus-eni.fr');
        $amelie->setPseudo('a_me_lie');
        $amelie->setRoles(['ROLE_USER']);
        $amelie->setName('Caillet');
        $amelie->setFirstName('Amélie');
        $amelie->setPhoneNumber('06' . $faker->numerify('########'));
        $amelie->setIsActive(true);
        $amelie->setProfileImage('/images/amelie.jpg');
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $amelie,
            'password123');
        $amelie->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $amelie->setCampus($randomCampus);
        $manager->persist($amelie);
        $this->addReference('amelie_caillet', $amelie);

        $paul = new User();
        $paul->setEmail('paul.perrot2024@campus-eni.fr');
        $paul->setPseudo('barking_boy');
        $paul->setRoles(['ROLE_USER']);
        $paul->setName('Perrot');
        $paul->setFirstName('Paul');
        $paul->setPhoneNumber('06' . $faker->numerify('########'));
        $paul->setIsActive(true);
        $paul->setProfileImage('/images/paul.jpg');
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $paul,
            'password123');
        $paul->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $paul->setCampus($randomCampus);
        $manager->persist($paul);
        $this->addReference('paul_perrot', $paul);

        $ghislain = new User();
        $ghislain->setEmail('ghislain.rouquette2024@campus-eni.fr');
        $ghislain->setPseudo('ghislain.rouquette');
        $ghislain->setRoles(['ROLE_USER']);
        $ghislain->setName('Rouquette');
        $ghislain->setFirstName('Ghislain');
        $ghislain->setPhoneNumber('06' . $faker->numerify('########'));
        $ghislain->setIsActive(true);
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $ghislain,
            'password123');
        $ghislain->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $ghislain->setCampus($randomCampus);
        $manager->persist($ghislain);

        $julian = new User();
        $julian->setEmail('julian.denoue2024@campus-eni.fr');
        $julian->setPseudo('jujute');
        $julian->setRoles(['ROLE_USER']);
        $julian->setName('Denoue');
        $julian->setFirstName('Julian');
        $julian->setPhoneNumber('06' . $faker->numerify('########'));
        $julian->setIsActive(true);
        $julian->setProfileImage('/images/julian.png');
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $julian,
            'password123');
        $julian->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $julian->setCampus($randomCampus);
        $manager->persist($julian);
        $this->addReference('julian_denoue', $julian);

        $tim = new User();
        $tim->setEmail('timothee.criaud2024@campus-eni.fr');
        $tim->setPseudo('feudai');
        $tim->setRoles(['ROLE_USER']);
        $tim->setName('Criaud');
        $tim->setFirstName('Timothée');
        $tim->setPhoneNumber('07' . $faker->numerify('########'));
        $tim->setIsActive(true);
        $tim->setProfileImage('/images/tim.jpg');
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $tim,
            'password123');
        $tim->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $tim->setCampus($randomCampus);
        $manager->persist($tim);
        $this->addReference('timothee_criaud', $tim);

        $antoine = new User();
        $antoine->setEmail('antoine.dequatremare2024@campus-eni.fr');
        $antoine->setPseudo('zeir');
        $antoine->setRoles(['ROLE_USER']);
        $antoine->setName('Dequatremare');
        $antoine->setFirstName('Antoine');
        $antoine->setPhoneNumber('07' . $faker->numerify('########'));
        $antoine->setIsActive(true);
        $antoine->setProfileImage('/images/antoine.png');
        //hacher le mdp
        $hashedPassword = $this->passwordHasher->hashPassword(
            $antoine,
            'password123');
        $antoine->setPassword($hashedPassword);
        //association campus aléatoire
        $randomCampus = $this->getReference('campus_' . rand(1, 4), Campus::class);
        $antoine->setCampus($randomCampus);
        $manager->persist($antoine);
        $this->addReference('antoine_dequatremare', $antoine);

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            CampusFixtures::class,
        ];
    }
}
