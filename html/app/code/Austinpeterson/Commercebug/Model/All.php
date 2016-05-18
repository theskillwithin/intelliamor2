<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Model;
use ReflectionClass;

class All
{
    static protected $collections=[];
    
    static public function asData()
    {
        return self::$collections;
    }
    
    static public function asJson()
    {
        return json_encode(self::$collections);
    }
    
    static public function getCollectionOfInformationForBlocks()
    {
        $key        = 'blocks';
        $block_info = self::getCollectionOfInformationFor($key);
        foreach(self::$collections[$key] as $object)
        {
            $class_key = get_class($object);
            $block_info[$class_key]['name'] = $object->getNameInLayout();
            $block_info[$class_key]['template'] = $object->getTemplateFile();
        }
        return $block_info;
    }
    
    static public function normalizeControllerInterceptors($data)
    {
        if(count($data) < 1)
        {
            return $data;
        }
        $new = [];
        foreach($data as $class=>$info)
        {
            $parts = [
                'file'      => $info['file'],
                'className' => $info['className'],
            ];
            $tmp['interceptor']   = $parts;
            
            $r = new ReflectionClass($info['className']);            
            $tmp['class']         = [
                'file'      => $r->getParentClass()->getFilename(),
                'className' => $r->getParentClass()->getName()
            ];
            
            $new[]          = $tmp;
        }
        
        return $new;
    }
    
    static public function getCollectionOfInformationFor($collection_name)
    {
        $models = self::getCollectionOf($collection_name);
        $model_info = [];
        foreach($models as $model)
        {
            $r = new ReflectionClass($model); 
            $class = get_class($model);
            if(!array_key_exists($class, $model_info))
            {
                $model_info[$class] = [
                    'times'=>0,
                    'file'=>$r->getFilename(),
                    'className'=>$class
                ];
            }
            $model_info[$class]['times']++;
        }        
        return $model_info;
    }
    
    static public function getCollectionOf($collection_name)
    {
        if(array_key_exists($collection_name, self::$collections))
        {
            return self::$collections[$collection_name];
        }
        return [];        
    }
    
    static public function addTo($collection_name, $item)
    {
        if(!array_key_exists($collection_name, self::$collections))
        {
            self::$collections[$collection_name] = [];
        }
        self::$collections[$collection_name][] = $item;
    }
    
    static public function mageDebugBacktrace($return=false, $html=true, $showFirst=false)
    {
        $d = debug_backtrace();
        $out = '';
        if ($html) $out .= "<pre>";
        foreach ($d as $i=>$r) {
            if (!$showFirst && $i==0) {
                continue;
            }
            // sometimes there is undefined index 'file'
            @$out .= "[$i] {$r['file']}:{$r['line']}\n";
        }
        if ($html) $out .= "</pre>";
        if ($return) {
            return $out;
        } else {
            echo $out;
        }
    }    
}