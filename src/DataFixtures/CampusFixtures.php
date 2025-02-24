<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nantesCampus = new Campus();
        $nantesCampus -> setName('NANTES');
        $manager->persist($nantesCampus);

        $rennesCampus = new Campus();
        $rennesCampus -> setName('RENNES');
        $manager->persist($rennesCampus);

        $niortCampus = new Campus();
        $niortCampus -> setName('NIORT');
        $manager->persist($niortCampus);

        $quimperCampus = new Campus();
        $quimperCampus -> setName('QUIMPER');
        $manager->persist($quimperCampus);

        $manager->flush();
    }
}
