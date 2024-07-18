<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\WrongDateException;
use App\Service\CurrencyExchange;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesController extends AbstractController
{
    private $currencyExchange;

    public function __construct(CurrencyExchange $currencyExchange)
    {
        $this->currencyExchange = $currencyExchange;
    }

    public function getExchangeRates(string $date): JsonResponse
    {
        try {
            $this->validate($date);
            $rates = $this->currencyExchange->getRatesByDate($date);
            return new JsonResponse($rates, Response::HTTP_OK);
        } catch (WrongDateException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Unexpected error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @throws WrongDateException
     */
    private function validate(string $date): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new WrongDateException('Incorrect date format');
        }
        try {
            $dateTime = new \DateTime($date);
        } catch (\Exception $e) {
            throw new WrongDateException('Incorrect date');
        }
        if ($dateTime < new \DateTime('2023-01-01')) {
            throw new WrongDateException('Date cannot be earlier than 2023-01-01');
        }
    }
}
