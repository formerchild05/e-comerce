<?php
namespace Nam\HelloWorld\Block;

use Magento\Framework\View\Element\Template;

class Hello extends Template
{
    public function getMessage()
    {
        return "Hello  21212 từ homepage!";
    }
}