<?php

namespace Nam\Weather\Block;

use Magento\Framework\View\Element\Template;
use Nam\Weather\Model\WeatherService;

class Weather extends Template
{
    protected $service;

    public function __construct(
        Template\Context $context,
        WeatherService $service,
        array $data = []
    ) {
        $this->service = $service;
        parent::__construct($context, $data);
    }

    public function getWeather($lat, $lon)
    {
        return $this->service->getWeather($lat, $lon);
    }
}