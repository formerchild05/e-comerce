<?php

namespace Minh\VNPay\Controller\Payment;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Minh\VNPay\Model\PaymentProcessor;

class Ipn implements HttpGetActionInterface
{
    private RequestInterface $request;
    private PaymentProcessor $paymentProcessor;
    private JsonFactory $jsonFactory;

    public function __construct(
        RequestInterface $request,
        PaymentProcessor $paymentProcessor,
        JsonFactory $jsonFactory
    ) {
        $this->request = $request;
        $this->paymentProcessor = $paymentProcessor;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        return $this->jsonFactory->create()->setData(
            $this->paymentProcessor->process($this->request->getParams())
        );
    }
}
