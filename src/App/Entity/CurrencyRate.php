<?php

namespace App\Entity;

use JsonSerializable;

class CurrencyRate implements JsonSerializable
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var string
     */
    private $date;

    /**
     * @var float|null
     */
    private $nbpRate;

    /**
     * @var float|null
     */
    private $buyRate;

    /**
     * @var float|null
     */
    private $sellRate;

    public function __construct(
        Currency $currency,
        string $date,
        ?float $nbpRate = null,
        ?float $buyRate = null,
        ?float $sellRate = null
    ) {
        $this->currency = $currency;
        $this->date = $date;
        $this->nbpRate = $nbpRate;
        $this->buyRate = $buyRate;
        $this->sellRate = $sellRate;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getNbpRate(): ?float
    {
        return $this->nbpRate;
    }

    /**
     * @return float
     */
    public function getBuyRate(): ?float
    {
        return $this->buyRate;
    }

    /**
     * @return float|null
     */
    public function getSellRate(): ?float
    {
        return $this->sellRate;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'currency' => $this->getCurrency(),
            'date' => $this->getDate(),
            'nbpRate' => $this->getNbpRate(),
            'buyRate' => $this->getBuyRate(),
            'sellRate' => $this->getSellRate(),
        ];
    }
}