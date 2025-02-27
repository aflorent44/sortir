<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer tous les utilisateurs et les stocker dans un tableau
        $users = $manager->getRepository(User::class)->findAll();
        if (empty($users)) {
            throw new \Exception("Aucun utilisateur trouvé. Assurez-vous que UserFixture s'exécute avant EventFixtures.");
        }

        // Récupérer toutes les adresses
        $addresses = $manager->getRepository(Address::class)->findAll();
        if (empty($addresses)) {
            throw new \Exception("Aucune adresse trouvée. Assurez-vous que AddressFixtures s'exécute avant EventFixtures.");
        }

        // Récupérer tous les campus
        $campuses = $manager->getRepository(Campus::class)->findAll();
        if (empty($campuses)) {
            throw new \Exception("Aucun campus trouvé. Assurez-vous que CampusFixtures s'exécute avant EventFixtures.");
        }

        // Pour debug
        echo "Nombre d'utilisateurs: " . count($users) . "\n";
        echo "Nombre d'adresses: " . count($addresses) . "\n";
        echo "Nombre de campus: " . count($campuses) . "\n";

        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $beginsAt = $faker->dateTimeBetween('+1 week', '+2 months');
            $endsAt = (clone $beginsAt)->modify('+' . $faker->numberBetween(1, 5) . ' hours');
            $registrationEndsAt = (clone $beginsAt)->modify('-' . $faker->numberBetween(3, 10) . ' days');

            $event->setName($faker->sentence(3))
                ->setBeginsAt(\DateTimeImmutable::createFromMutable($beginsAt))
                ->setEndsAt(\DateTimeImmutable::createFromMutable($endsAt))
                ->setRegistrationEndsAt(\DateTimeImmutable::createFromMutable($registrationEndsAt))
                ->setDescription($faker->paragraph)
                ->setMaxParticipantNumber($faker->numberBetween(5, 50));

            if (!empty($addresses)) {
                $event->setAddress($faker->randomElement($addresses));
            }

            if (!empty($users)) {
                $randomUser = $users[array_rand($users)];
                $event->setHost($randomUser);
            }

            if (!empty($campuses)) {
                $randomCampus = $faker->randomElement($campuses);
                $event->addCampus($randomCampus);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            AddressFixtures::class,
            CampusFixtures::class,
        ];
    }

}


