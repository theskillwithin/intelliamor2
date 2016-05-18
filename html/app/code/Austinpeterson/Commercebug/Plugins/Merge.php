<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Plugins;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
class Merge
{
    public function beforeAddHandle($subject, $handleName)
    {
        $handleName = is_array($handleName) ? $handleName : [$handleName];
        
        foreach($handleName as $handle)
        {
            \Austinpeterson\Commercebug\Model\All::addTo('handles', $handle);                    
        }
        $args = func_get_args();
        array_shift($args);
        return $args;        
    }

    public function afterLoad($subject, $result)
    {
        $this->afterLoadGetPackageLayout($subject, $result);
        $this->afterLoadGetPackageLayoutFiles($subject, $result);
        return $result;
    }
    
    protected function getPrivatePropertyFromObject($subject, $propName)
    {
        $r = new ReflectionClass($subject);
        $prop     = false;
        try
        {
            $prop = $r->getProperty($propName);
        }
        catch(ReflectionException $e)
        {
            while($r = $r->getParentClass())
            {
                try
                {
                    $prop = $r->getProperty($propName);                                        
                }
                catch(ReflectionException $e){}
                
            }
        }
        if(!$prop)
        {
            return false;
        }
        $prop->setAccessible(true);     
        return $prop->getValue($subject);    
    }
    
    protected function callProtectedReflectedMethod($subject, $method, $args)
    {
        $r = new ReflectionClass($subject);
        $method = $r->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($subject, $args);
    }
    
    protected function afterLoadGetPackageLayoutFiles($subject, $result)
    {
        $fileSource = $this->getPrivatePropertyFromObject($subject, 'fileSource');        
        $pageLayoutFileSource = $this->getPrivatePropertyFromObject(
            $subject, 'pageLayoutFileSource'); 

        $theme      = $this->getPrivatePropertyFromObject($subject, 'theme');
        $theme      = $this->callProtectedReflectedMethod(
            $subject, '_getPhysicalTheme', [$theme]);

        if(!$fileSource || !$theme || !$pageLayoutFileSource)
        {
            return $result;
        }
        $updateFiles = $fileSource->getFiles($theme, '*.xml');
        $updateFiles = array_merge(
            $updateFiles, $pageLayoutFileSource->getFiles($theme, '*.xml'));
        $files = array_map(function($o){
            return $o->getFilename();
        }, $updateFiles);
        \Austinpeterson\Commercebug\Model\All::addTo(
            'page_layout_xmlfile', $files);
    }
    
    protected function afterLoadGetPackageLayout($subject, $result)
    {
        \Austinpeterson\Commercebug\Model\All::addTo(
            'page_layout_xml', $subject->getFileLayoutUpdatesXml()->asXml());                 
    }
}