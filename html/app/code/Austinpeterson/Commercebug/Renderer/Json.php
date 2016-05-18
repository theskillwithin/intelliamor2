<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Renderer;
use Austinpeterson\Commercebug\Model\All;
class Json
{
    protected $data;
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function clean($data)
    {
        foreach($data as $key=>$value)
        {
            if($key === 'invoked_observers')
            {
                $data[$key] = array_filter($value, function($val){
                    return strpos($val['instance'], 'Austinpeterson\\Commercebug') === false;
                });
            }
            
            if(in_array($key,['models','collections']))
            {
                $data[$key] = All::getCollectionOfInformationFor($key);
            }
            
            if($key === 'blocks')
            {
                $data[$key] = All::getCollectionOfInformationForBlocks();
            }            
            
            if($key === 'controllers')
            {
                $data[$key] = All::getCollectionOfInformationFor($key);
                $data[$key] = All::normalizeControllerInterceptors($data[$key]);
            }                 
        }

        $data['layouts'] = [
            'graph'=> \Austinpeterson\Commercebug\Plugins\MagentoFrameworkViewLayout::renderGraph(),
            'nonce'=> md5 (md5( date('Y-m-d', strToTime("-0day")) ) . 'not a drill')
        ];
        
        $data['other-files'] = get_included_files();
        
        foreach(['blocks','collections','controllers',
        'dispatched_events','handles','invoked_observers',
        'layouts','models','other-files','server'] as $key)
        {
            if(!isset($data[$key]))
            {
                $data[$key] = [];
            }
        }
        return $data;   
    }
    
    public function render()
    {
        $data = $this->clean($this->data);
        return '<script type="text/javascript">' . 
        'pulsestorm_commerbug_json = '           . 
        json_encode($data, 
            JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS) . 
            ';'                                  .
        '</script>';
        
    }
}