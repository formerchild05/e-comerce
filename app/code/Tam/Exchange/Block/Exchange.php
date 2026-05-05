<?php

namespace Tam\Exchange\Block;

use Magento\Framework\View\Element\Template;
use Tam\Exchange\Model\ExchangeService;

class Exchange extends Template
{
    private ExchangeService $exchangeService;

    public function __construct(
        Template\Context $context,
        ExchangeService $exchangeService,
        array $data = []
    ) {
        $this->exchangeService = $exchangeService;
        parent::__construct($context, $data);
    }

    public function getExchangeData(): array
    {
        return $this->exchangeService->getRates();
    }
}
