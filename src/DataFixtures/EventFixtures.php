<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 15; $i++) {
            $event = new Event();
            $event->setName($faker->word)
                ->setDescription($faker->text)
                ->setMaxParticipantNumber(rand(1, 10))
                ->setHost($this->getReference('host_' . rand(1, 10), User::class))
                ->setBeginsAt($faker->dateTime)
                ->setEndsAt($faker->dateTime)
                ->setRegistrationEndsAt($faker->dateTime);
            $manager->persist($event);
        }
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CampusFixtures::class,
            UserFixture::class,
        ];
    }
}
