<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Model;
use Magento\Framework\DataObject\IdentityInterface;
class Log extends \Magento\Framework\Model\AbstractModel implements LogInterface, IdentityInterface
{
    const CACHE_TAG = 'pulsestorm_commercebug_log';

    protected function _construct()
    {
        $this->_init('Austinpeterson\Commercebug\Model\ResourceModel\Log');
    }

    protected function purgeOldRecords()
    {
        $sql = 'DELETE FROM ' . $this->getResource()->getMainTable() . 
            ' WHERE pulsestorm_commercebug_log_id NOT IN ( 
              SELECT pulsestorm_commercebug_log_id 
              FROM ( 
                SELECT pulsestorm_commercebug_log_id 
                FROM ' . $this->getResource()->getMainTable() . ' 
                ORDER BY pulsestorm_commercebug_log_id DESC 
                LIMIT 10
              ) x 
            );';

        $query = $this->getResource()->getConnection()->query($sql);
        $query->execute();
    }
    
    public function logData($data)
    {
        $this->setData(['json_log'=>json_encode($data)])
        ->save();                         
        $this->purgeOldRecords();       
    }
    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
