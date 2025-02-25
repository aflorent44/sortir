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
        $this->addReference('campus_1', $nantesCampus);

        $rennesCampus = new Campus();
        $rennesCampus -> setName('RENNES');
        $manager->persist($rennesCampus);
        $this->addReference('campus_2', $rennesCampus);

        $niortCampus = new Campus();
        $niortCampus -> setName('NIORT');
        $manager->persist($niortCampus);
        $this->addReference('campus_3', $niortCampus);

        $quimperCampus = new Campus();
        $quimperCampus -> setName('QUIMPER');
        $manager->persist($quimperCampus);
        $this->addReference('campus_4', $quimperCampus);

        $manager->flush();
    }
}
