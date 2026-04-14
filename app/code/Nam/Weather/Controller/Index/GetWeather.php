<?php

namespace Nam\Weather\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Nam\Weather\Model\WeatherService;

class GetWeather extends Action implements CsrfAwareActionInterface
{
    private JsonFactory $resultJsonFactory;
    private WeatherService $weatherService;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        WeatherService $weatherService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->weatherService = $weatherService;
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception processing is needed
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return true if custom validation failed
     */
    public function validateForCsrf(RequestInterface $request): ?bool {
        return true; // Disable CSRF validation
    }

    public function execute()
    {
        $request = $this->getRequest();
        $lat = $request->getParam('lat');
        $lon = $request->getParam('lon');

        if (!$lat || !$lon) {
            $result = $this->resultJsonFactory->create();
            return $result->setData(['error' => 'Missing latitude or longitude']);
        }

        try {
            $weatherData = $this->weatherService->getWeather($lat, $lon);
            $result = $this->resultJsonFactory->create();
            return $result->setData(['weather' => $weatherData]);
        } catch (\Exception $e) {
            $result = $this->resultJsonFactory->create();
            return $result->setData([
                'error' => $e->getMessage(),
                'code' => is_int($e->getCode()) ? $e->getCode() : 500
            ]);
        }
    }
}
