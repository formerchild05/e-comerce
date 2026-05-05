<?php

namespace Tam\Exchange\Observer;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class AddExchangeLinkToTopmenu implements ObserverInterface
{
    private NodeFactory $nodeFactory;
    private UrlInterface $urlBuilder;

    public function __construct(
        NodeFactory $nodeFactory,
        UrlInterface $urlBuilder
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(Observer $observer): void
    {
        $menu = $observer->getData('menu');
        if (!$menu) {
            return;
        }

        $tree = $menu->getTree();

        $menu->addChild($this->nodeFactory->create([
            'data' => [
                'name' => __('Tỷ giá'),
                'id' => 'exchange-link',
                'url' => $this->urlBuilder->getUrl('exchange'),
                'is_active' => false,
                'class' => 'level-top',
            ],
            'idField' => 'id',
            'tree' => $tree,
        ]));
    }
}
