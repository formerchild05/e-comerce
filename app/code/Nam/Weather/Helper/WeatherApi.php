<?php

namespace Nam\Weather\Helper;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\HTTP\Client\Curl;

class WeatherApi
{
    private const ENV_API_KEY = 'OPENWEATHER_API_KEY';
    private const DEPLOYMENT_CONFIG_PATH = 'weather/openweathermap/api_key';

    private $curl;
    private $deploymentConfig;

    public function __construct(
        Curl $curl,
        DeploymentConfig $deploymentConfig
    )
    {
        $this->curl = $curl;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function fetchWeatherData($lat, $lon)
    {
        $apiKey = $this->getApiKey();
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

    private function getApiKey(): string
    {
        $apiKey = (string) ($this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_PATH) ?? getenv(self::ENV_API_KEY));
        $apiKey = trim($apiKey);

        if ($apiKey === '') {
            throw new \RuntimeException(
                'OpenWeatherMap API key is missing. Set env var ' . self::ENV_API_KEY
                . ' or app/etc/env.php value at path ' . self::DEPLOYMENT_CONFIG_PATH
            );
        }

        return $apiKey;
    }
}