<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Plugins;

class MagentoFrameworkViewLayout
{
    static protected $renderingQueue;
    static protected $graphs=[];
    static protected $graph_styles=[];
    static protected $renderedElements = array();
    static protected $layout;
    static protected $structure;

    const QUOTE = '"';
    public function __construct(
        \Magento\Framework\View\Layout\Data\Structure $structure
    )
    {
        self::$structure = $structure;
    }
    
    public function beforeRenderNonCachedElement($subject, $name)
    {
        if(!self::$layout)
        {
            self::$layout = $subject;
        }    
        self::$renderingQueue[]   = $name;
        self::$renderedElements[] = $name;        

        $args = func_get_args();
        array_shift($args);
        return $args;
    }
    
    public function afterRenderNonCachedElement($subject, $result)
    {
        $name = array_pop(self::$renderingQueue);    
        return $result;
    }    
    
    static protected function buildStyle($element)
    {        
        $attributes = [];
        $attributes['label'] = $element;
        if(self::$layout->isContainer($element))
        {
            $attributes['fillcolor'] = 'lightgrey';
            $attributes['style']     = 'filled';            
        }

        if(self::$layout->isUiComponent($element))
        {
            $attributes['fillcolor'] = 'green';
            $attributes['style']     = 'filled';            
        }
         
        if(self::$layout->isBlock($element))
        {
            $block = self::$layout->getBlock($element);
            $class = get_class($block);
            $attributes['label'] .= '\n' . str_replace("\\","\\\\",$class);
            
            if($block->getTemplate())
            {
                $attributes['label'] .= '\n' . str_replace("\\","\\\\",$block->getTemplate());
            }
        }
        
        $style = '"'.$element.'"[';
        foreach($attributes as $key=>$attribute)
        {
            $q = '"';

            $style .= $key . "=$q" . $attribute. "$q ";     
        }
        $style = trim($style);
        $style .= ']';
        
        return $style;
        
    }
    
    static protected function buildStyles($parent, $element)
    {
        if($parent)
        {
            self::$graph_styles[$parent] = self::buildStyle($parent);
        }
        
        if($element)
        {
            self::$graph_styles[$element] = self::buildStyle($element);
        }        
    }
    
    static protected function buildGraphs()
    {
        $child_to_parent_lookup = [];        
        foreach(self::$renderedElements as $element)
        {
            foreach(self::$layout->getChildNames($element) as $child)
            {
                $child_to_parent_lookup[$child] = $element;
            }
        }
        
        foreach(self::$renderedElements as $element)
        {
            $parent = array_key_exists($element, $child_to_parent_lookup) ? $child_to_parent_lookup[$element] : false;            
            self::buildStyles($parent, $element);            

            if($parent)
            {
                self::$graphs[] = '"' . $parent . '"->"' . $element . '"';
                continue;
            }
            self::$graphs[] = '"' . $element . '"';
        }     
                
        return self::$graphs;
    }
    
    static public function renderGraph()
    {
        self::buildGraphs();
        $graph_template = 'digraph g {
    ranksep=6
    node [
        fontsize = "16"
        shape = "rectangle"
        width =3
        height =.5
    ];
    edge [
    ];         
    
    ##DOT HERE##
}';  
        $lines  = implode("\n", self::$graphs) . "\n";
        $lines .= implode("\n", self::$graph_styles);
        
        return str_replace('##DOT HERE##', $lines, $graph_template);      
    }
}