<?php

namespace Minh\VNPay\Controller\Payment;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Minh\VNPay\Model\PaymentUrlBuilder;

class Start implements HttpGetActionInterface
{
    private CheckoutSession $checkoutSession;
    private PaymentUrlBuilder $paymentUrlBuilder;
    private RedirectFactory $redirectFactory;

    public function __construct(
        CheckoutSession $checkoutSession,
        PaymentUrlBuilder $paymentUrlBuilder,
        RedirectFactory $redirectFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $order = $this->checkoutSession->getLastRealOrder();

        try {
            if (!$order || !$order->getId()) {
                throw new LocalizedException(__('Order is not available for VNPay payment.'));
            }

            return $resultRedirect->setUrl($this->paymentUrlBuilder->build($order));
        } catch (\Throwable $throwable) {
            $this->checkoutSession->restoreQuote();
            $this->checkoutSession->setErrorMessage($throwable->getMessage());
            return $resultRedirect->setPath('checkout/cart');
        }
    }
}
