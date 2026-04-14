<?php
namespace Nam\Weather\Observer;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class WeatherButton implements ObserverInterface
{
    private NodeFactory $nodeFactory;
    private UrlInterface $urlBuilder;   
    public function __construct(NodeFactory $nodeFactory, UrlInterface $urlBuilder)
    {
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

        $nodeData = [
            'name' => __('Weather'),
            'id' => 'weather-link',
            'url' => $this->urlBuilder->getUrl('weather'),
            'is_active' => false,
            'class' => 'level-top',
        ];

        $menu->addChild($this->nodeFactory->create([
            'data' => $nodeData,
            'idField' => 'id',
            'tree' => $tree,
        ]));
    }


}