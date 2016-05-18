<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Model;
use \ReflectionClass;
class RenderedData 
{
    protected $data;
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function getCollectionOfInformationFor($type)
    {
        $models = $this->getCollectionOf($type);
        $model_info = [];
        foreach($models as $model)
        {
            $r = new ReflectionClass($model); 
            $class = get_class($model);
            if(!array_key_exists($class, $model_info))
            {
                $model_info[$class] = [
                    'times'=>0,
                    'file'=>$r->getFilename()
                ];
            }
            $model_info[$class]['times']++;
        }        
        return $model_info;        
    }
    
    public function getCollectionOf($type)
    {
        if(array_key_exists($type, $this->data))
        {
            return $this->data[$type];
        }
        return [];     
    }
}