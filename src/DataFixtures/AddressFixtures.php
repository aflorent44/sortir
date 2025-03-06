<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
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
            ->setLat(mt_rand(44000, 50000) / 1000) // Latitude entre 44.000 et 50.000
            ->setLng(mt_rand(1000, 6000) / 1000) // Longitude entre 1.000 et 6.000
            ->setCity($faker->city) // Ville
            ->setZipCode($faker->postcode) // Code postal
            ->setIsAllowed(true);
            $this->addReference('address_' . $i, $address);
            $manager->persist($address);
        }

        $manager->flush();
    }

}
