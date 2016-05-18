<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Model\ResourceModel\Log;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Austinpeterson\Commercebug\Model\Log','Austinpeterson\Commercebug\Model\ResourceModel\Log');
    }
}
