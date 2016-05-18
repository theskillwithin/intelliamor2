<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Observers;
class Controller extends AbstractObserver
{
    protected function _execute(\Magento\Framework\Event\Observer $observer)
    {
        return $this->getControllerInformation($observer);
    }
    
    public function getControllerInformation($observer)
    {
        \Austinpeterson\Commercebug\Model\All::addTo('controllers',
            $observer->getControllerAction()
        );
    }
}