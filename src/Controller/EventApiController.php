<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/events', name: 'api_events_')]
class EventApiController extends AbstractController
{

    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(EventRepository $eventRepository) : JsonResponse
    {

        $events = $eventRepository->findAll();
        // Transformer les entités en tableau
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'lat' => $event->getAddress()->getLat(),
                'lng' => $event->getAddress()->getLng(),
                'status' => $event->getStatus()->value,
                'description' => $event->getDescription(),
                // Ajoutez d'autres propriétés selon vos besoins
            ];
        }

        return $this->json($data);
    }

}
