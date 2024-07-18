<?php
namespace Integration\ExchangeRates;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesTest extends WebTestCase
{
    private $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    public function testExchangeRatesSuccess(): void
    {
        $this->httpClient->request('GET', '/api/exchange-rates');
        $response = $this->httpClient->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testExchangeRatesIncorrectFormat(): void
    {
        $this->httpClient->request('GET', '/api/exchange-rates/20iA-01-01');
        $response = $this->httpClient->getResponse();
        $content = json_decode($response->getContent());

        $this->assertTrue(isset($content->error));
        $this->assertEquals('Incorrect date format', $content->error);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testExchangeRatesFutureDate(): void
    {
        $this->httpClient->request('GET', '/api/exchange-rates/2010-01-01');
        $response = $this->httpClient->getResponse();
        $content = json_decode($response->getContent());

        $this->assertTrue(isset($content->error));
        $this->assertEquals('Date cannot be earlier than 2023-01-01', $content->error);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
