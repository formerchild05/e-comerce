<?php
namespace Nam\Weather\Model;

use Nam\Weather\Helper\WeatherApi;
class WeatherService
{
    private $api;

    public function __construct(WeatherApi $api)
    {
        $this->api = $api;
    }

    public function getWeather($lat, $lon)
    {
        $result = $this->api->fetchWeatherData($lat, $lon);
        return $result;
    }
}