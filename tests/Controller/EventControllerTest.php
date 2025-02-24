<?php

namespace App\Tests\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EventControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $eventRepository;
    private string $path = '/event/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->eventRepository = $this->manager->getRepository(Event::class);

        foreach ($this->eventRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Event index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'event[name]' => 'Testing',
            'event[beginsAt]' => 'Testing',
            'event[endsAt]' => 'Testing',
            'event[duration]' => 'Testing',
            'event[registrationEndsAt]' => 'Testing',
            'event[description]' => 'Testing',
            'event[maxParticipantNumber]' => 'Testing',
            'event[status]' => 'Testing',
            'event[campuses]' => 'Testing',
            'event[address]' => 'Testing',
            'event[host]' => 'Testing',
            'event[participants]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->eventRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Event();
        $fixture->setName('My Title');
        $fixture->setBeginsAt('My Title');
        $fixture->setEndsAt('My Title');
        $fixture->setDuration('My Title');
        $fixture->setRegistrationEndsAt('My Title');
        $fixture->setDescription('My Title');
        $fixture->setMaxParticipantNumber('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCampuses('My Title');
        $fixture->setAddress('My Title');
        $fixture->setHost('My Title');
        $fixture->setParticipants('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Event');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Event();
        $fixture->setName('Value');
        $fixture->setBeginsAt('Value');
        $fixture->setEndsAt('Value');
        $fixture->setDuration('Value');
        $fixture->setRegistrationEndsAt('Value');
        $fixture->setDescription('Value');
        $fixture->setMaxParticipantNumber('Value');
        $fixture->setStatus('Value');
        $fixture->setCampuses('Value');
        $fixture->setAddress('Value');
        $fixture->setHost('Value');
        $fixture->setParticipants('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'event[name]' => 'Something New',
            'event[beginsAt]' => 'Something New',
            'event[endsAt]' => 'Something New',
            'event[duration]' => 'Something New',
            'event[registrationEndsAt]' => 'Something New',
            'event[description]' => 'Something New',
            'event[maxParticipantNumber]' => 'Something New',
            'event[status]' => 'Something New',
            'event[campuses]' => 'Something New',
            'event[address]' => 'Something New',
            'event[host]' => 'Something New',
            'event[participants]' => 'Something New',
        ]);

        self::assertResponseRedirects('/event/');

        $fixture = $this->eventRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getBeginsAt());
        self::assertSame('Something New', $fixture[0]->getEndsAt());
        self::assertSame('Something New', $fixture[0]->getDuration());
        self::assertSame('Something New', $fixture[0]->getRegistrationEndsAt());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getMaxParticipantNumber());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getCampuses());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getHost());
        self::assertSame('Something New', $fixture[0]->getParticipants());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Event();
        $fixture->setName('Value');
        $fixture->setBeginsAt('Value');
        $fixture->setEndsAt('Value');
        $fixture->setDuration('Value');
        $fixture->setRegistrationEndsAt('Value');
        $fixture->setDescription('Value');
        $fixture->setMaxParticipantNumber('Value');
        $fixture->setStatus('Value');
        $fixture->setCampuses('Value');
        $fixture->setAddress('Value');
        $fixture->setHost('Value');
        $fixture->setParticipants('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/event/');
        self::assertSame(0, $this->eventRepository->count([]));
    }
}
