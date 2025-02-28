<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Event;
use App\Enum\EventStatus;
use App\EventListener\EventStatusListener;
use App\Form\AddressType;
use App\Form\CancelType;
use App\Form\EventType;
use App\Form\FilterType;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Service\EventRegistrationService;
use ContainerMjX9Puf\getConsole_ErrorListenerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class EventController extends AbstractController
{

    #[Route('/', name: 'app_event_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EventRepository $eventRepository, EventStatusListener $eventStatusListener): Response
    {

        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $isHost = $form->get('isHost')->getData(); // Vérifier s'il veut filtrer en tant qu'hôte
        $isParticipant = $form->get('isParticipant')->getData(); // Vérifier s'il veut filtrer en tant que participant
        $isNotParticipant = $form->get('isNotParticipant')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $campus = $form->get('campus')->getData();
            $name = $form->get('name')->getData();
            $dateMin = $form->get('dateMin')->getData();
            $dateMax = $form->get('dateMax')->getData();
            $dateMax = $form->get('dateMax')->getData();
            $status = $form->get('ended')->getData();

            $events = $eventRepository->findByFilters($campus, $name, $dateMin, $dateMax, $status, $user, $isHost, $isParticipant, $isNotParticipant);
        } else {
            $events = $eventRepository->findAll();
        }

        $eventStatusListener->updateAllEventsStatus($events);

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'result' => count($events),
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
            if ($eventForm->getClickedButton() === $eventForm->get('save')) {
                $event->setStatus(EventStatus::CREATED);
            } elseif ($eventForm->getClickedButton() === $eventForm->get('publish')) {
                $event->setStatus(EventStatus::OPENED);
            }
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

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
        // Vérifier si l'événement est en statut CREATED
        if ($event->getStatus() !== EventStatus::CREATED) {
            $this->addFlash('error', 'Seuls les événements avec le statut "créée" peuvent être modifiés.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        $address = $event->getAddress() ?? new Address();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid() && $addressForm->isSubmitted() && $addressForm->isValid()) {
            if ($eventForm->getClickedButton() === $eventForm->get('save')) {
                $event->setStatus(EventStatus::CREATED);
            } elseif ($eventForm->getClickedButton() === $eventForm->get('publish')) {
                $event->setStatus(EventStatus::OPENED);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'eventForm' => $eventForm,
            'addressForm' => $addressForm,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_event_cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CancelType::class, $event);
        //$request->setMethod('POST');
        $form->handleRequest($request);
        dump($request->getMethod());
        if ($form->isSubmitted() && $form->isValid()) {
            dump("Formulaire soumis");

            if ($form->get('submit')->isClicked()) {
                dump("Bouton cliqué");

                if ($this->isCsrfTokenValid('cancel_event', $request->request->get('_token'))) {
                    dump("Token valide");

                    if ($event->getHost() == $this->getUser() &&
                        in_array($event->getStatus(), [EventStatus::OPENED, EventStatus::CLOSED, EventStatus::CREATED])) {

                        $cancelReason = $form->get('cancelReason')->getData();
                        dump("Motif d'annulation : " . $cancelReason);

                        $event->setStatus(EventStatus::CANCELLED);
                        $entityManager->flush();

                        $this->addFlash("success", "Nous vous confirmons l'annulation de cette sortie");

                        //return $this->redirectToRoute('app_event_index');
                    } else {
                        $this->addFlash("error", "Impossible de supprimer cette sortie");
                    }
                } else {
                    dump("Token invalide !");
                }
            }
        }
        dump("fin de fonction");
        if ($request->isMethod('POST')) {
            dump("C'est bien une requête POST");
        } else {
            dump("GET 2");
        }
        return $this->render('event/cancel.html.twig', [
            'event' => $event,
            'cancelForm' => $form,
        ]);
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
