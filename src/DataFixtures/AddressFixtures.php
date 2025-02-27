<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Génération de données en français

        for ($i = 0; $i < 20; $i++) {
            $address = new Address();
            $address->setName($faker->company) // Nom du lieu
            ->setStreet($faker->streetAddress) // Rue et numéro
            ->setLat($faker->latitude) // Latitude aléatoire
            ->setLng($faker->longitude) // Longitude aléatoire
            ->setCity($faker->city) // Ville
            ->setZipCode($faker->postcode); // Code postal

            $manager->persist($address);
        }

        $manager->flush();
    }
}
