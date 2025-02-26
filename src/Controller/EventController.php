<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Event;
use App\Enum\EventStatus;
use App\EventListener\EventStatusListener;
use App\Form\AddressType;
use App\Form\EventType;
use App\Form\FilterType;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Service\EventRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{

    #[Route('/', name: 'app_event_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EventRepository $eventRepository, EventStatusListener $eventStatusListener): Response
    {

        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campus = $form->get('campus')->getData();
            if ($campus) {
                $events = $eventRepository->findByCampus($campus);
                $this->addFlash('info', 'Filtrage par campus: ' . $campus->getName());
            } else {
                $events = $eventRepository->findAll();
            }
        } else {
            $events = $eventRepository->findAll();
        }

        $eventStatusListener->updateAllEventsStatus($events);
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'filterForm' => $form,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.e pour créer une sortie.');
        }

        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid() && $addressForm->isSubmitted() && $addressForm->isValid()) {

            $entityManager->persist($address);
            $event->setAddress($address);
            $event->setHost($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }
        dump($event);

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'eventForm' => $eventForm,
            'addressForm' => $addressForm,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event, EventStatusListener $eventStatusListener): Response
    {
        $eventStatusListener->updateOneEventStatus($event);
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $address = $event->getAddress() ?? new Address();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid() && $addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'eventForm' => $eventForm,
            'addressForm' => $addressForm,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_event_cancel', methods: ['POST'])]
    public function cancel(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {

        if ($event->getHost() == $this->getUser() && ($event->getStatus() == EventStatus::OPENED || $event->getStatus() == EventStatus::CLOSED)) {
            $event->setStatus(EventStatus::CANCELLED);
            $entityManager->flush();
            $this->addFlash("success", "Nous vous confirmons l'annulation de cette sortie");
        } else {
            $this->addFlash("error", "Impossible de supprimer cette sortie");
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/register', name: 'app_event_register', methods: ['POST'])]
    public function register(Event $event, EntityManagerInterface $entityManager, EventRegistrationService $eventRegistrationService): Response
    {
        if ($eventRegistrationService->registerUser($event, $this->getUser())) {
            $this->addFlash('success', 'Vous êtes bien inscrit.e pour la sortie "' . $event->getName() . '"');
        } else {
            $this->addFlash('error', 'Il n\'est pas possible de s\'inscrire à cette sortie');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cancelRegistration', name: 'app_event_cancel_registration', methods: ['POST'])]
    public function cancelRegistration(Event $event, EntityManagerInterface $entityManager, EventRegistrationService $eventRegistrationService): Response
    {
        if ($eventRegistrationService->unregisterUser($event, $this->getUser())) {
            $this->addFlash('success', 'Nous vous confirmons l\'annulation de votre inscription à la sortie "' . $event->getName() . '"');
        } else {
            $this->addFlash('error', 'Impossible d\'annuler votre inscription à cette sortie');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }


}
