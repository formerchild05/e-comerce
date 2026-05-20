<?php

namespace Minh\VNPay\Model;

use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;

class PaymentProcessor
{
    public const RSP_SUCCESS = '00';
    public const RSP_ORDER_NOT_FOUND = '01';
    public const RSP_ALREADY_CONFIRMED = '02';
    public const RSP_INVALID_AMOUNT = '04';
    public const RSP_INVALID_SIGNATURE = '97';
    public const RSP_UNKNOWN = '99';

    private VNPayConfig $config;
    private Signature $signature;
    private OrderFactory $orderFactory;
    private OrderRepositoryInterface $orderRepository;
    private InvoiceService $invoiceService;
    private TransactionFactory $transactionFactory;
    private InvoiceSender $invoiceSender;

    public function __construct(
        VNPayConfig $config,
        Signature $signature,
        OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        InvoiceSender $invoiceSender
    ) {
        $this->config = $config;
        $this->signature = $signature;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
    }

    public function process(array $params): array
    {
        $incrementId = (string) ($params['vnp_TxnRef'] ?? '');
        $order = $this->loadOrder($incrementId);

        if (!$order) {
            return $this->response(self::RSP_ORDER_NOT_FOUND, 'Order not found');
        }

        if (!$this->signature->verify($params, $this->config->getHashSecret($order->getStoreId()))) {
            return $this->response(self::RSP_INVALID_SIGNATURE, 'Invalid signature');
        }

        $expectedAmount = (int) round(((float) $order->getGrandTotal()) * 100);
        $paidAmount = (int) ($params['vnp_Amount'] ?? 0);
        if ($expectedAmount !== $paidAmount) {
            return $this->response(self::RSP_INVALID_AMOUNT, 'Invalid amount');
        }

        $payment = $order->getPayment();
        $transactionId = (string) ($params['vnp_TransactionNo'] ?? $params['vnp_BankTranNo'] ?? '');
        if ($payment && $transactionId !== '' && $payment->getLastTransId() === $transactionId) {
            return $this->response(self::RSP_ALREADY_CONFIRMED, 'Order already confirmed');
        }

        if (($params['vnp_ResponseCode'] ?? '') !== self::RSP_SUCCESS || ($params['vnp_TransactionStatus'] ?? '') !== self::RSP_SUCCESS) {
            $order->addCommentToStatusHistory(__('VNPay payment failed. Response code: %1', $params['vnp_ResponseCode'] ?? ''));
            $this->orderRepository->save($order);
            return $this->response(self::RSP_SUCCESS, 'Payment failed recorded');
        }

        $this->markOrderPaid($order, $transactionId, $params);

        return $this->response(self::RSP_SUCCESS, 'Confirm success');
    }

    public function isSuccessfulReturn(array $params): bool
    {
        $order = $this->loadOrder((string) ($params['vnp_TxnRef'] ?? ''));

        return $order
            && $this->signature->verify($params, $this->config->getHashSecret($order->getStoreId()))
            && ($params['vnp_ResponseCode'] ?? '') === self::RSP_SUCCESS
            && ($params['vnp_TransactionStatus'] ?? '') === self::RSP_SUCCESS;
    }

    private function loadOrder(string $incrementId): ?Order
    {
        if ($incrementId === '') {
            return null;
        }

        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        return $order->getId() ? $order : null;
    }

    private function markOrderPaid(Order $order, string $transactionId, array $params): void
    {
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId ?: (string) ($params['vnp_TxnRef'] ?? ''));
        $payment->setLastTransId($transactionId ?: (string) ($params['vnp_TxnRef'] ?? ''));
        $payment->setAdditionalInformation('vnpay_response', $this->filterSensitiveParams($params));
        $order->addCommentToStatusHistory(__('VNPay payment confirmed. Transaction: %1', $transactionId));

        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);

            $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $this->invoiceSender->send($invoice);
        } else {
            $this->orderRepository->save($order);
        }
    }

    private function response(string $code, string $message): array
    {
        return [
            'RspCode' => $code,
            'Message' => $message,
        ];
    }

    private function filterSensitiveParams(array $params): array
    {
        unset($params['vnp_SecureHash']);
        return $params;
    }
}
