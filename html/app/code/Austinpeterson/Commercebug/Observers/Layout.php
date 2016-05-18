<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Observers;
class Layout extends AbstractObserver
{
    protected function _execute(\Magento\Framework\Event\Observer $observer)
    {
        return $this->getLayoutInformation($observer);
    }
    
    public function getLayoutInformation($observer)
    {
//         $layout = $observer->getLayout();
//         var_dump($layout);
//         var_dump(__METHOD__);
//         exit;
    }
}