<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiService
{
    public const URI = 'http://api.aladhan.com/v1/calendar';
    private HttpClientInterface $client;

    /**
     * ApiService constructor.
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getData(int $method, string $longitude, string $latitude, int $year, int $month): ResponseInterface
    {
        return $this->client->request(
            'GET',
            static::URI . "?method={$method}&longitude={$longitude}&latitude={$latitude}&month={$month}&year={$year}"
        );

    }

}