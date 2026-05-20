<?php

namespace Minh\ZaloPay\Model;

use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;

class PaymentProcessor
{
    private OrderFactory $orderFactory;
    private OrderRepositoryInterface $orderRepository;
    private InvoiceService $invoiceService;
    private TransactionFactory $transactionFactory;
    private InvoiceSender $invoiceSender;
    private Signature $signature;
    private ZaloPayConfig $config;
    private Json $json;

    public function __construct(
        OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        InvoiceSender $invoiceSender,
        Signature $signature,
        ZaloPayConfig $config,
        Json $json
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
        $this->signature = $signature;
        $this->config = $config;
        $this->json = $json;
    }

    public function processCallback(string $data, string $mac): array
    {
        if (!$this->signature->verify($data, $mac, $this->config->getKey2())) {
            return ['return_code' => 2, 'return_message' => 'Invalid'];
        }

        $payload = $this->json->unserialize($data);
        $order = $this->loadOrderByAppTransId((string) ($payload['app_trans_id'] ?? ''));
        if (!$order) {
            return ['return_code' => 2, 'return_message' => 'Invalid'];
        }

        $this->markOrderPaid($order, (string) ($payload['zp_trans_id'] ?? ''), $payload);
        return ['return_code' => 1, 'return_message' => 'Success'];
    }

    public function processQueryResult(Order $order, array $queryResult): bool
    {
        if ((int) ($queryResult['return_code'] ?? 0) !== 1) {
            $order->addCommentToStatusHistory(
                __('ZaloPay payment is not confirmed yet. Message: %1', $queryResult['return_message'] ?? '')
            );
            $this->orderRepository->save($order);
            return false;
        }

        $this->markOrderPaid($order, (string) ($queryResult['zp_trans_id'] ?? ''), $queryResult);
        return true;
    }

    public function loadOrderByAppTransId(string $appTransId): ?Order
    {
        if ($appTransId === '' || strpos($appTransId, '_') === false) {
            return null;
        }

        $parts = explode('_', $appTransId, 2);
        $incrementId = $parts[1] ?? '';
        if ($incrementId === '') {
            return null;
        }

        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        return $order->getId() ? $order : null;
    }

    private function markOrderPaid(Order $order, string $transactionId, array $payload): void
    {
        $payment = $order->getPayment();
        $transactionId = $transactionId !== '' ? $transactionId : (string) ($payload['app_trans_id'] ?? $order->getIncrementId());

        if ($payment && $payment->getLastTransId() === $transactionId) {
            return;
        }

        $payment->setTransactionId($transactionId);
        $payment->setLastTransId($transactionId);
        $payment->setAdditionalInformation('zalopay_response', $payload);

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
        }

        $order->addCommentToStatusHistory(__('ZaloPay payment confirmed. Transaction: %1', $transactionId));
        $this->orderRepository->save($order);
    }
}
