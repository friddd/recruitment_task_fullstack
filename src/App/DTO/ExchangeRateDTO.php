<?php
namespace App\DTO;

use App\Entity\CurrencyRate;
use JsonSerializable;

class ExchangeRateDTO implements JsonSerializable
{
    /**
     * @var CurrencyRate
     */
    private $currentRate;

    /**
     * @var CurrencyRate
     */
    private $selectedRate;

    public function __construct(CurrencyRate $currentRate, CurrencyRate $selectedRate)
    {
        $this->currentRate = $currentRate;
        $this->selectedRate = $selectedRate;
    }

    /**
     * @return CurrencyRate
     */
    public function getCurrentRate(): CurrencyRate
    {
        return $this->currentRate;
    }

    /**
     * @return CurrencyRate
     */
    public function getSelectedRate(): CurrencyRate
    {
        return $this->selectedRate;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'currentRate' => $this->getCurrentRate(),
            'selectedRate' => $this->getSelectedRate(),
        ];
    }
}