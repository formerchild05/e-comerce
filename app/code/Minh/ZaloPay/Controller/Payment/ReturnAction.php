<?php

namespace Minh\ZaloPay\Controller\Payment;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Minh\ZaloPay\Model\Gateway;
use Minh\ZaloPay\Model\PaymentProcessor;

class ReturnAction implements HttpGetActionInterface
{
    private RequestInterface $request;
    private Gateway $gateway;
    private PaymentProcessor $paymentProcessor;
    private RedirectFactory $redirectFactory;
    private ManagerInterface $messageManager;

    public function __construct(
        RequestInterface $request,
        Gateway $gateway,
        PaymentProcessor $paymentProcessor,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->gateway = $gateway;
        $this->paymentProcessor = $paymentProcessor;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $appTransId = (string) $this->request->getParam('apptransid', $this->request->getParam('app_trans_id'));
        $order = $this->paymentProcessor->loadOrderByAppTransId($appTransId);

        if (!$order) {
            $this->messageManager->addErrorMessage(__('ZaloPay order was not found.'));
            return $resultRedirect->setPath('checkout/cart');
        }

        $queryResult = $this->gateway->queryOrder($appTransId, $order->getStoreId());
        if ($this->paymentProcessor->processQueryResult($order, $queryResult)) {
            return $resultRedirect->setPath('checkout/onepage/success');
        }

        $this->messageManager->addNoticeMessage(__('ZaloPay payment is processing or was not completed.'));
        return $resultRedirect->setPath('checkout/cart');
    }
}
