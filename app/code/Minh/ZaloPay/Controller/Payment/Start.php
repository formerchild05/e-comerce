<?php

namespace Minh\ZaloPay\Controller\Payment;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Minh\ZaloPay\Model\Gateway;

class Start implements HttpGetActionInterface
{
    private CheckoutSession $checkoutSession;
    private Gateway $gateway;
    private RedirectFactory $redirectFactory;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        CheckoutSession $checkoutSession,
        Gateway $gateway,
        RedirectFactory $redirectFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->gateway = $gateway;
        $this->redirectFactory = $redirectFactory;
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $order = $this->checkoutSession->getLastRealOrder();

        try {
            if (!$order || !$order->getId()) {
                throw new LocalizedException(__('Order is not available for ZaloPay payment.'));
            }

            $response = $this->gateway->createOrder($order);
            if ((int) ($response['return_code'] ?? 0) !== 1 || empty($response['order_url'])) {
                throw new LocalizedException(__($response['return_message'] ?? 'Could not create ZaloPay order.'));
            }

            $order->getPayment()->setAdditionalInformation('zalopay_create_order', $response);
            $this->orderRepository->save($order);
            return $resultRedirect->setUrl((string) $response['order_url']);
        } catch (\Throwable $throwable) {
            $this->checkoutSession->restoreQuote();
            $this->checkoutSession->setErrorMessage($throwable->getMessage());
            return $resultRedirect->setPath('checkout/cart');
        }
    }
}
