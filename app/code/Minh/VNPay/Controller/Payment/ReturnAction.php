<?php

namespace Minh\VNPay\Controller\Payment;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Minh\VNPay\Model\PaymentProcessor;

class ReturnAction implements HttpGetActionInterface
{
    private RequestInterface $request;
    private PaymentProcessor $paymentProcessor;
    private RedirectFactory $redirectFactory;
    private ManagerInterface $messageManager;

    public function __construct(
        RequestInterface $request,
        PaymentProcessor $paymentProcessor,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->paymentProcessor = $paymentProcessor;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $params = $this->request->getParams();

        if ($this->paymentProcessor->isSuccessfulReturn($params)) {
            return $resultRedirect->setPath('checkout/onepage/success');
        }

        $this->messageManager->addErrorMessage(__('VNPay payment was not completed.'));
        return $resultRedirect->setPath('checkout/cart');
    }
}
