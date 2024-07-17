<?php

namespace App\Service;

use App\DTO\ExchangeRateDTO;
use App\Entity\Currency;
use App\Entity\CurrencyRate;

class CurrencyExchange
{
    /**
     * @var NbpApi
     */
    private $nbpApi;

    /**
     * @var array
     */
    private $supportedCurrencyCodes;

    private const CURRENCY_USD = 'USD';
    private const CURRENCY_EUR = 'EUR';

    private const EUR_USD_BUY_SPREAD = 0.05;
    private const EUR_USD_SELL_SPREAD = 0.07;
    private const OTHER_SELL_SPREAD = 0.15;

    public function __construct(NbpApi $nbpApi, array $currencyCodes)
    {
        $this->nbpApi = $nbpApi;
        $this->supportedCurrencyCodes = $currencyCodes;
    }

    /**
     * @param string $date
     * @return ExchangeRateDTO[]
     */
    public function getRatesByDate(string $date): array
    {
        $rates = [];

        foreach ($this->getSupportedCurrencyCodes() as $currencyCode) {
            $today = new \DateTime();
            $currentRate = $this->getCurrencyRate($currencyCode, $today->format('Y-m-d'));
            $selectedRate = $this->getCurrencyRate($currencyCode, $date);
            $rates[] = new ExchangeRateDTO($currentRate, $selectedRate);
        }

        return $rates;
    }

    /**
     * @param string $currencyCode
     * @param string $date
     * @return CurrencyRate
     */
    public function getCurrencyRate(string $currencyCode, string $date): CurrencyRate
    {
        try {
            $nbpExchangeRate = $this->nbpApi->getExchangeRate($currencyCode, $date);
            $currencyName = ucfirst($nbpExchangeRate['currency']);
            $currency = new Currency($currencyCode, $currencyName);
            $nbpRate = $nbpExchangeRate['rate'];
            $buyRate = $this->calculateBuyRate($currencyCode, $nbpRate);
            $sellRate = $this->calculateSellRate($currencyCode, $nbpRate);

            return new CurrencyRate($currency, $date, $nbpRate, $buyRate, $sellRate);
        }
        catch (\Throwable $tex) {
            return new CurrencyRate(new Currency($currencyCode), $date);
        }
    }

    private function getSupportedCurrencyCodes(): array
    {
        return $this->supportedCurrencyCodes;
    }

    /**
     * @param string $currencyCode
     * @param float $nbpRate
     * @return float|null
     */
    private function calculateBuyRate(string $currencyCode, float $nbpRate): ?float
    {
        if (!$nbpRate || !$this->isEuroOrUsd($currencyCode)) {
            return null;
        }

        return $nbpRate - self::EUR_USD_BUY_SPREAD;
    }

    /**
     * @param string $currencyCode
     * @param float $nbpRate
     * @return float|null
     */
    private function calculateSellRate(string $currencyCode, float $nbpRate): ?float
    {
        if (!$nbpRate) {
            return null;
        }

        if ($this->isEuroOrUsd($currencyCode)) {
            return $nbpRate + self::EUR_USD_SELL_SPREAD;
        }

        return $nbpRate + self::OTHER_SELL_SPREAD;
    }

    /**
     * @param string $currencyCode
     * @return bool
     */
    private function isEuroOrUsd(string $currencyCode): bool
    {
        return in_array($currencyCode, [self::CURRENCY_EUR, self::CURRENCY_USD]);
    }
}