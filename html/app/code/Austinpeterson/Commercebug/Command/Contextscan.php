<?php
namespace Austinpeterson\Commercebug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\ObjectManagerInterface;
use ReflectionClass;        
class Contextscan extends Command
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $name = null)
    {
        return parent::__construct($name);
    }
    
    protected function configure()
    {
        $this->setName("ps:contextscan");
        $this->setDescription("Scans class for DI params repeated in context object.")
        ->addArgument(
            'class',
            InputArgument::REQUIRED,
            'Class to Scan');
        parent::configure();
    }

    protected function getConstructorParams($reflection_class)
    {
        return $reflection_class->getMethod('__construct')
            ->getParameters();
        
    }
    
    protected function getContextObjectFromParamList($params)
    {
        $context = false;
        foreach($params as $param)
        {
            $hint = $param->getClass();
            if(!$hint){continue;}
            if($hint->implementsInterface('Magento\Framework\ObjectManager\ContextInterface'))
            {
                $context = $hint;
                break;
            }
        }    
        return $context;    
    }
    
    protected function getClassFromFile($class)
    {
        $contents = file_get_contents($class);
        preg_match('%namespace(.+?);%',$contents, $matches);
        $namespace = $matches[1];
        
        preg_match('%class\s+?([a-zA-Z_]+)%',$contents, $matches);
        $class = $matches[1];
        $class = trim($namespace) . '\\' . $class;
        return $class;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class      = $input->getArgument('class');
        if(file_exists($class))
        {
            $class = $this->getClassFromFile($class);
        }        
        
        $r          = new ReflectionClass($class);
        $params     = $this->getConstructorParams($r);
        $context    = $this->getContextObjectFromParamList($params);        

        if(!$context)
        {
            $output->writeln("Could not find context object in $class"); 
            return; 
        }
        
        $params_from_context = $this->getConstructorParams($context);
        
        $callback = function($param){
            $hint = $param->getClass();
            $hint = $hint ? $hint->getName() : false;
            return $hint;
        };
        
        $params_from_construct = array_filter(array_map($callback, $params));
        $params_from_context   = array_filter(array_map($callback, $params_from_context));
        
        $dupes = array_intersect($params_from_construct, $params_from_context);
        
        if(count($dupes) < 1)
        {
            $output->writeln("No intersections found.");
            return;
        }
        
        $output->writeln("Found param in original object that's in context object.");
        array_map(function($dupe) use ($output){
            $output->writeln(' - ' . $dupe);
            return $dupe;
        },$dupes);
        
        $output->writeln('Original Object: ' . $r->getFilename());
        $output->writeln('Context  Object: ' . $context->getFilename());        
    }
} 