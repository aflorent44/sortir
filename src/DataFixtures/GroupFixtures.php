<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        //récupère les users existants
        $users = $manager->getRepository(User::class)->findAll();

        if (count($users) <2) {
            throw new \Exception('Minimum 2 utilisateurs sont nécessaire pour créer des groupes.');
        }

        for ($i = 1; $i <= 5; $i++) {
            $group = new Group();
            $group->setName($faker->word());

            //définition aléatoire d'un owner
            $owner = $users[array_rand($users)];
            $group->setOwner($owner);

            //ajouter des membres aléatoire
            shuffle($users);
            foreach (array_slice($users, 0, rand(1, count($users))) as $member) {
                $group->addMember($member);
            }
            $manager->persist($group);
        }

        //récupérer des user précis pour faire des groupes
        $yoann = $this->getReference('yoann_battu', User::class);
        $amelie = $this->getReference('amelie_caillet', User::class);
        $paul = $this->getReference('paul_perrot', User::class);
        $julian = $this->getReference('julian_denoue', User::class);
        $tim = $this->getReference('timothee_criaud', User::class);
        $antoine = $this->getReference('antoine_dequatremare', User::class);

        $group = new Group();
        $group->setName('PAT');
        $group->setOwner($paul);
        $group->addMember($antoine);
        $group->addMember($tim);

        $manager->persist($group);

        $group2 = new Group();
        $group2->setName('Dark Yoyo');
        $group2->setOwner($yoann);
        $group2->addMember($amelie);
        $group2->addMember($julian);

        $manager->persist($group2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
