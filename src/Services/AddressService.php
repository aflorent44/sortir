<?php

namespace App\Services;

use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AddressService extends AbstractController
{
    private AddressRepository $addressRepository;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function findAddress(Request $request): JsonResponse
    {
        $query = $request->query->get('req', '');

        if (empty($query)) {
            return $this->json(['error' => 'Aucune ville spécifiée'], 400);
        }

        // URL de l'API gouvernementale
        $apiUrl = "https://geo.api.gouv.fr/communes?nom=" . urlencode($query) . "&limit=5";

        try {
            $response = $this->httpClient->request('GET', $apiUrl);
            $data = $response->toArray();

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de la requête API'], 500);
        }
    }
}