<?php

namespace App\Service;

use App\Exception\NotFoundException;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpApi
{
    /** @var string  */
    private $apiUrl;

    /** @var HttpClientInterface  */
    private $httpClient;

    public function __construct()
    {
        $this->apiUrl = 'https://api.nbp.pl/api/';
        $this->httpClient = HttpClient::create();
    }

    /**
     * @param string $code
     * @param string $date
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws NotFoundException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getExchangeRate(string $code, string $date): array
    {
        $endpoint = sprintf("exchangerates/rates/a/%s/%s?format=json", $code, $date);
        $response = $this->httpClient->request(Request::METHOD_GET, $this->apiUrl . $endpoint);
        $status = $response->getStatusCode();

        if ($status == 200) {
            $responseData = $response->toArray();
            return [
                'currency' => $responseData['currency'],
                'rate' => $responseData['rates'][0]['mid']
            ];
        }
        if ($status == 404) {
            throw new NotFoundException(sprintf("%s exchange rate on %s not found.", $code, $date));
        }

        throw new Exception("Unexpected error.");
    }
}