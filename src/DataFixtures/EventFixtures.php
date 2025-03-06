<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Address;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création manuelle des événements avec des données fixes
        $events = [
            [
                'name' => 'Conférence Symfony 2025',
                'description' => 'Venez découvrir les dernières nouveautés de Symfony et échanger avec les développeurs.',
                'city' => 'Paris',
                'campus' => 'campus_1',
                'beginAt' => new \DateTimeImmutable('2025-05-15 09:00:00'),
                'endAt' => new \DateTimeImmutable('2025-05-15 17:00:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-05-10 23:59:59'),
                'status' => EventStatus::OPENED,
                'maxParticipants' => 50,
                'host' => 'admin',
                'participants' => ['user_1', 'user_2', 'user_3'],
                'address' => 'address_1',
            ],
            [
                'name' => 'Hackathon Campus ENI',
                'description' => 'Participez à un hackathon de 24 heures sur des projets open source.',
                'city' => 'Lyon',
                'campus' => 'campus_2',
                'beginAt' => new \DateTimeImmutable('2025-06-01 10:00:00'),
                'endAt' => new \DateTimeImmutable('2025-06-02 10:00:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-05-28 23:59:59'),
                'status' => EventStatus::PENDING,
                'maxParticipants' => 30,
                'host' => 'user_4',
                'participants' => ['user_5', 'user_6'],
                'address' => 'address_2',
            ],
            [
                'name' => 'Soirée Réseautage',
                'description' => 'Un événement pour rencontrer d\'autres professionnels du secteur tech.',
                'city' => 'Marseille',
                'campus' => 'campus_3',
                'beginAt' => new \DateTimeImmutable('2025-07-10 18:00:00'),
                'endAt' => new \DateTimeImmutable('2025-07-10 22:00:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-07-05 23:59:59'),
                'status' => EventStatus::CLOSED,
                'maxParticipants' => 100,
                'host' => 'admin',
                'participants' => ['user_7', 'user_8'],
                'address' => 'address_3',
            ],
            [
                'name' => 'Conférence sur la cybersécurité',
                'city' => 'Paris',
                'campus' => 'campus_1',
                'beginAt' => new \DateTimeImmutable('2025-04-10 09:00:00'),
                'endAt' => new \DateTimeImmutable('2025-04-10 17:00:00'),
                'description' => 'Un événement pour discuter des dernières tendances en cybersécurité.',
                'registrationEndAt' => new \DateTimeImmutable('2025-04-08 23:59:59'),
                'status' => EventStatus::OPENED,
                'maxParticipants' => 100,
                'host' => 'admin',
                'participants' => ['user_7', 'user_8'],
                'address' => 'address_3',
            ],
            [
                'name' => 'Soirée étudiante à la plage',
                'city' => 'Nice',
                'campus' => 'campus_4',
                'beginAt' => new \DateTimeImmutable('2025-03-05 18:00:00'),
                'endAt' => new \DateTimeImmutable('2025-03-05 23:59:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-03-01 23:59:59'),
                'description' => 'Venez passer une soirée inoubliable avec des activités sur la plage.',
                'status' => EventStatus::ENDED,
                'maxParticipants' => 8,
                'host' => 'admin',
                'participants' => ['user_7', 'user_8'],
                'address' => 'address_1',
            ],
            [
                'name' => 'Hackathon – Développeurs unis',
                'city' => 'Lyon',
                'campus' => 'campus_2',
                'beginAt' => new \DateTimeImmutable('2025-02-01 09:00:00'),
                'endAt' => new \DateTimeImmutable('2025-02-03 20:00:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-02-30 23:59:59'),
                'description' => 'Un hackathon de 48h pour développer des projets innovants autour de l’intelligence artificielle.',
                'status' => EventStatus::ARCHIVED,
                'maxParticipants' => 50,
                'host' => 'admin',
                'participants' => ['user_7', 'user_8'],
                'address' => 'address_3',
            ],
            [
                'name' => 'Concert de charité',
                'city' => 'Marseille',
                'campus' => 'campus_4',
                'beginAt' => new \DateTimeImmutable('2025-07-20 19:00:00'),
                'endAt' => new \DateTimeImmutable('2025-07-20 22:00:00'),
                'registrationEndAt' => new \DateTimeImmutable('2025-07-18 23:59:59'),
                'description' => 'Concert de charité organisé pour soutenir les victimes des catastrophes naturelles.',
                'status' => EventStatus::CREATED,
                'maxParticipants' => 500,
                'host' => 'yoann_battu',
                'participants' => ['user_1', 'user_3', 'user_6'],
                'address' => 'address_4',
            ],
            [
                'name' => 'Séminaire sur l\'entrepreneuriat',
                'city' => 'Toulouse',
                'campus' => 'campus_1',
                'beginAt' => new \DateTimeImmutable('2025-03-09 10:00:00'),
                'endAt' => new \DateTimeImmutable('2025-03-09 17:00:00'),
                'description' => 'Un séminaire de formation sur l\'entrepreneuriat et le financement des startups.',
                'registrationEndAt' => new \DateTimeImmutable('2025-03-07 23:59:59'),
                'status' => EventStatus::OPENED,
                'maxParticipants' => 10,
                'host' => 'user_2',
                'participants' => ['admin', 'user_8'],
                'address' => 'address_1',
            ]
        ];

        foreach ($events as $eventData) {
            $event = new Event();

            $event->setName($eventData['name'])
                ->setDescription($eventData['description'])
                ->setBeginsAt($eventData['beginAt'])
                ->setEndsAt($eventData['endAt'])
                ->setRegistrationEndsAt($eventData['registrationEndAt'])
                ->setStatus($eventData['status'])
                ->setMaxParticipantNumber($eventData['maxParticipants']);

            $event->setAddress($this->getReference($eventData['address'], Address::class));
            $event->setHost($this->getReference($eventData['host'], User::class));
            $event->addCampus($this->getReference($eventData['campus'], Campus::class));

            foreach ($eventData['participants'] as $participantReference) {
                $event->addParticipant($this->getReference($participantReference, User::class));
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
