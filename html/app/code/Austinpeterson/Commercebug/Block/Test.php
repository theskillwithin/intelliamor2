<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Block;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Austinpeterson\Commercebug\Model\ViewVars;
use ReflectionClass;
class Test extends Template //AbstractBlock
{
    public function __construct(Template\Context $context, array $data = [], ViewVars $viewVars)
    {
        $this->viewVars = $viewVars;
        parent::__construct($context, $data);
    }
    
    protected function getViewVars()
    {
        return $this->viewVars;
    }
    
    protected function _prepareLayout()
    {
        $r = new ReflectionClass($this->viewVars->getData('class_to_lookup_name'));
        
        $interfaces = class_implements($r->getName());
        $interfaces = $interfaces ? $interfaces : [];
        $implements = [];        
        foreach($interfaces as $interface)
        {
            $reflectInterface = new ReflectionClass($interface);
            $implements[]     = $this->formatClass($reflectInterface);
        }
        $this->viewVars->setImplements($implements);
         
        $extends = [];
        while($r = $r->getParentClass())
        {
            $extends[] = $this->formatClass($r);
        }        
        $this->viewVars->setExtends($extends);
    }
    
    protected function formatClass($reflection_class)
    {
        $name = $reflection_class->getName();
        $file = $reflection_class->getFilename();
        $file = $file ? $file : 'PHP built-in';
        return '<div style="margin-bottom:5px;"><pre class="pulsestorm_commercebug_phpclass">'.$name.'</pre>' . "\n" .
        '<pre class="pulsestorm_commercebug_file">'.$file.'</pre></div>';
        
    }
    
//     public function _toHtml()
//     {
//         return '<p>Hello There Again</p>';
//     }
}