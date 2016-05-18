<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Plugin\Magento\Framework\View\Asset;
use Magento\Framework\View\Asset\AssetInterface;

class GroupedCollection
{
    protected $developerHelper;
    protected $state;
    public function __construct(
        \Magento\Developer\Helper\Data $developerHelper,
        \Magento\Framework\App\State $state    
    )
    {
        $this->developerHelper  = $developerHelper;
        $this->state            = $state;
    }
    //function beforeMETHOD($subject, $arg1, $arg2){}
    //function aroundMETHOD($subject, $procede, $arg1, $arg2){return $proceed($arg1, $arg2);}
    //function afterMETHOD($subject, $result){return $result;}
    function aroundAdd($subject, $procede, $identifier, AssetInterface $asset, array $properties = []){
        if(!$this->shouldApplyCommercebug($identifier))
        {
            return;
        }
        return $procede($identifier, $asset, $properties);
    }    
    
    protected function shouldApplyCommercebug($identifier)
    {
        $is_area_frontend     = $this->state->getAreaCode() === 'frontend';
        $asset_is_commercebug = strpos($identifier, 'Austinpeterson_Commercebug::') !== false;
        $dev_is_allowed       = $this->developerHelper->isDevAllowed();
        
        if(!$asset_is_commercebug)
        {
            return true;
        }
        
        // if(!$is_area_frontend)
        // {
        //     return true;
        // }
        
        return $dev_is_allowed;
    }
}
