<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Controller\Lookup;
use ReflectionClass;

class Index extends \Austinpeterson\Commercebug\Controller\AbstractController
{    
    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
        $class = false;
        $class_from_manager = false;
        if(array_key_exists('lookup', $_POST))
        {
            $class  = $_POST['lookup'];
            $r      = new ReflectionClass($class);
            
            if(!$this->shouldSkipInstantiation($class))
            {         

                $class_from_manager = get_class($this->objectManager->get($class));
                $rm = new ReflectionClass($class_from_manager);
            }            
        }
        
        if($class)
        {
            $this->viewVars->setClassToLookupName($class);
            $this->viewVars->setClassToLookupPath($r->getFileName());
        }
        
        if($class_from_manager)
        {
            $this->viewVars->setObjectManagerClassName($class_from_manager);
            $this->viewVars->setObjectManagerClassPath($rm->getFileName());
        }
                
        $this->viewVars->setConstructorParams([]);                
        try
        {
            $method = $r->getMethod('__construct');
        }
        catch(\Exception $e)
        {
            $method = false;
        }
        if($class && $method)
        {
            $r = new ReflectionClass($class);
            $method = $r->getMethod('__construct');
            $params = [];
            foreach($method->getParameters() as $param)
            {
                if($param->getClass())
                {
                    $params[$param->getName()] = $param->getClass()->getName();
                }
                else
                {
                    $params[$param->getName()] = 'UnTypedOrArray';
                }
            }
            $this->viewVars->setConstructorParams($params);
        }                
        $resultPage = $this->resultPageFactory->create();                
        
        return $this->resultPageFactory->create();                
    }
}
