<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Address;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer toutes les adresses existantes en base de données
        $addresses = $manager->getRepository(Address::class)->findAll();
        // Récupérer tous les utilisateurs existants en base de données
        $users = $manager->getRepository(User::class)->findAll();

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
                ->setMaxParticipantNumber($faker->numberBetween(5, 50))
                ->setStatus($faker->randomElement(EventStatus::cases()));

            if (!empty($addresses)) {
                $event->setAddress($faker->randomElement($addresses));
            }

            if (!empty($users)) {
                $event->setHost($faker->randomElement($users));
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}
