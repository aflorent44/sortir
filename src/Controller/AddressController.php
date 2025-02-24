<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AddressController extends AbstractController
{
    private AddressRepository $addressRepository;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function findAddress(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (empty($query)) {
            return $this->json(['error' => 'Aucune ville spécifiée'], 400);
        }

        // URL de l'API gouvernementale
        $apiUrl = "https://geo.api.gouv.fr/communes?nom=" . urlencode($query);

        try {
            $response = $this->httpClient->request('GET', $apiUrl);
            $data = $response->toArray();

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de la requête API'], 500);
        }
    }
    public function find(Request $request): JsonResponse
    {
        $query = $request->query->get('q', ''); // Récupère la saisie de l'utilisateur
        $cities = $this->addressRepository->findByKeyword($query); // Requête personnalisée

        return $this->json($cities);
    }

    #[Route('/address', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'controller_name' => 'AddressController',
        ]);
    }
}
