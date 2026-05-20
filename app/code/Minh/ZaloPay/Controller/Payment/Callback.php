<?php

namespace Minh\ZaloPay\Controller\Payment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Minh\ZaloPay\Model\PaymentProcessor;

class Callback implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;
    private JsonFactory $jsonFactory;
    private Json $json;
    private PaymentProcessor $paymentProcessor;

    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        Json $json,
        PaymentProcessor $paymentProcessor
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->json = $json;
        $this->paymentProcessor = $paymentProcessor;
    }

    public function execute()
    {
        $body = (string) $this->request->getContent();
        $payload = $body !== '' ? $this->json->unserialize($body) : $this->request->getParams();

        return $this->jsonFactory->create()->setData(
            $this->paymentProcessor->processCallback(
                (string) ($payload['data'] ?? ''),
                (string) ($payload['mac'] ?? '')
            )
        );
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
