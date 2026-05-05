<?php

namespace Tam\Exchange\Model;

use Magento\Framework\HTTP\Client\Curl;
use RuntimeException;

class ExchangeService
{
    private const SOURCE_URL = 'https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx?b=68';

    private Curl $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function getRates(): array
    {
        $this->curl->setOption(CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $this->curl->setOption(CURLOPT_TIMEOUT, 15);
        $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 8);
        $this->curl->setOption(CURLOPT_FOLLOWLOCATION, true);

        $this->curl->get(self::SOURCE_URL);
        $response = trim((string) $this->curl->getBody());
        $status = $this->curl->getStatus();

        if ($status !== 200) {
            throw new RuntimeException('Vietcombank API error (HTTP ' . $status . ')');
        }

        if ($response === '') {
            throw new RuntimeException('Vietcombank API returned empty response');
        }

        $xml = @simplexml_load_string($response);
        if ($xml === false) {
            throw new RuntimeException('Vietcombank API returned invalid XML');
        }

        $rates = [];
        foreach ($xml->Exrate as $item) {
            $rates[] = [
                'currency_code' => (string) $item['CurrencyCode'],
                'currency_name' => trim((string) $item['CurrencyName']),
                'buy' => (string) $item['Buy'],
                'transfer' => (string) $item['Transfer'],
                'sell' => (string) $item['Sell'],
            ];
        }

        return [
            'date_time' => (string) ($xml->DateTime ?? ''),
            'rates' => $rates,
            'source_url' => self::SOURCE_URL,
        ];
    }
}
