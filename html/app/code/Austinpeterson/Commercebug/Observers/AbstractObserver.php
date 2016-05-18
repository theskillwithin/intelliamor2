<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Observers;
abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    static protected $_calling=false;
    public function __construct(
        \Magento\Developer\Helper\Data $developerHelper      
    )
    {
        $this->developerHelper = $developerHelper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(self::$_calling)
        {
            return;
        }
        self::$_calling = true;
        if(!$this->developerHelper->isDevAllowed())
        {
            self::$_calling = false;
            return;
        }
        self::$_calling = false;
        return $this->_execute($observer);
    }
    
    abstract protected function _execute(\Magento\Framework\Event\Observer $observer);
}