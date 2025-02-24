<?php

namespace App\Services;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityApiService
{
    public function __construct(private HttpClientInterface $client,) {}

    public function fetchGitHubInformation(): array
    {
        $apiUrl = $_ENV['CITY_API_URL'];
        $response = $this->client->request(
            'GET',
            $apiUrl
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }
}