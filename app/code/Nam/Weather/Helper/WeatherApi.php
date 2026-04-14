<?php

namespace Nam\Weather\Helper;

use Magento\Framework\HTTP\Client\Curl;

class WeatherApi
{
    private $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function fetchWeatherData($lat, $lon)
    {
        $apiKey = 'ea9c7d98a5d7b20f4f98adcf7e877e0f';
        $query = http_build_query([
            'lat' => $lat,
            'lon' => $lon,
            'units' => 'metric',
            'appid' => $apiKey,
        ]);
        $apiUrl = 'https://api.openweathermap.org/data/2.5/weather?' . $query;

        $this->curl->setOption(CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $this->curl->setOption(CURLOPT_TIMEOUT, 10);
        $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 5);

        $this->curl->get($apiUrl);
        $response = $this->curl->getBody();
        $httpCode = $this->curl->getStatus();

        if ($httpCode !== 200) {
            $errorMsg = substr($response, 0, 200);
            if (empty($errorMsg)) {
                $errorMsg = 'No response body';
            }
            throw new \Exception('OpenWeatherMap API error (HTTP ' . $httpCode . '): ' . $errorMsg);
        }

        if (empty($response)) {
            throw new \Exception('OpenWeatherMap API returned empty response');
        }

        $data = json_decode($response, true);
        if ($data === null) {
            throw new \Exception('OpenWeatherMap API returned invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }
}