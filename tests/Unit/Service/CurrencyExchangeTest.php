<?php

namespace Unit\Service;
use App\Exception\NotFoundException;
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

    public function testCurrentRateDateIsToday(): void
    {
        $expectedDate = (new \DateTime())->format('Y-m-d');
        $this->nbpApiMock
            ->method('getLastTableDate')
            ->willThrowException(new \Exception);
        $actualRates = $this->currencyExchange->getRatesByDate('2024-01-01');
        $this->assertEquals($expectedDate, $actualRates[0]->getCurrentRate()->getDate());
    }

    public function testCurrencyRateWithEmptyCurrencyNameAndRates(): void
    {
        $this->nbpApiMock
            ->method('getExchangeRate')
            ->willThrowException(new NotFoundException);
        $actualCurrencyRate = $this->currencyExchange->getCurrencyRate('USD','2024-01-01');
        $this->assertNull($actualCurrencyRate->getCurrency()->getName());
        $this->assertNull($actualCurrencyRate->getNbpRate());
        $this->assertNull($actualCurrencyRate->getBuyRate());
        $this->assertNull($actualCurrencyRate->getSellRate());
    }

    /**
     * @dataProvider sellRateProvider
     */
    public function testSellRateCalculate(string $currencyCode, string $currencyName, float $nbpRate, float $expectedRate): void
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

    public function sellRateProvider(): array
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
    public function testBuyRateCalculate(string $currencyCode, string $currencyName, float $nbpRate, ?float $expectedRate): void
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

    public function buyRateProvider(): array
    {
        return [
            ['EUR', 'euro', 4.2904, 4.2404],
            ['USD', 'dolar amerykański', 3.9110, 3.8610],
            ['CZK', 'korona czeska', 0.1694, null],
            ['IDR', 'rupia indonezyjska', 0.0002, null],
        ];
    }

}