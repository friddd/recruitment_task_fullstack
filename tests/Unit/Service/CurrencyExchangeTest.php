<?php

namespace Unit\Service;
use PHPUnit\Framework\TestCase;
use App\Service\CurrencyExchange;
use App\Service\NbpApi;

class CurrencyExchangeTest extends TestCase
{
    private $nbpApiMock;
    private $currencyExchange;

    protected function setUp(): void
    {
        $this->nbpApiMock = $this->createMock(NbpApi::class);
        $currencyCodes = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];
        $this->currencyExchange = new CurrencyExchange($this->nbpApiMock, $currencyCodes);
    }

    /**
     * @dataProvider sellRateProvider
     */
    public function testSellRateCalculate(string $currencyCode, string $currencyName, float $nbpRate, float $expectedRate)
    {
        $this->nbpApiMock
            ->method('getExchangeRate')
            ->willReturn([
                'currency' => $currencyName,
                'rate' => $nbpRate
            ]);
        $actualCurrencyRate = $this->currencyExchange->getCurrencyRate($currencyCode, '2024-01-01');

        $this->assertEquals($expectedRate, $actualCurrencyRate->getSellRate());
    }

    public function sellRateProvider()
    {
        return [
            ['EUR', 'euro', 4.2904, 4.3604],
            ['USD', 'dolar amerykański', 3.9110, 3.9810],
            ['CZK', 'korona czeska', 0.1694, 0.3194],
            ['IDR', 'rupia indonezyjska', 0.0002, 0.1502],
        ];
    }

    /**
     * @dataProvider buyRateProvider
     */
    public function testBuyRateCalculate(string $currencyCode, string $currencyName, float $nbpRate, ?float $expectedRate)
    {
        $this->nbpApiMock
            ->method('getExchangeRate')
            ->willReturn([
                'currency' => $currencyName,
                'rate' => $nbpRate
            ]);
        $actualCurrencyRate = $this->currencyExchange->getCurrencyRate($currencyCode, '2024-01-01');

        $this->assertEquals($expectedRate, $actualCurrencyRate->getBuyRate());
    }

    public function buyRateProvider()
    {
        return [
            ['EUR', 'euro', 4.2904, 4.2404],
            ['USD', 'dolar amerykański', 3.9110, 3.8610],
            ['CZK', 'korona czeska', 0.1694, null],
            ['IDR', 'rupia indonezyjska', 0.0002, null],
        ];
    }

}