<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Command;
use ReflectionClass;
use Exception;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Checkevents extends Command
{
    protected function configure()
    {
        $this->setName("ps:cb:check-events");
        $this->setDescription("A command the programmer was too lazy to enter a description for.");
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xml = simplexml_load_file('app/code/Austinpeterson/Commercebug/etc/events.xml');
        foreach($xml->event as $event)
        {
            foreach($event->observer as $observer)
            {
                $class = (string) $observer['instance'];
                $output->writeln("Checking $class");
                
                $short_class = explode("\\", $class);
                $short_class = array_pop($short_class);
                
                $r = new ReflectionClass($class);                
                $execute_method  = $r->getMethod('execute');
                $original_method = $r->getMethod($short_class);
                
                $method          = $short_class;
                $method[0]       = strToLower($method[0]);
                $line_execute    = 'return $this->' . $method . '($observer);';
                $contents        = file_get_contents($r->getFilename());
                if(strpos($contents, $line_execute) === false)
                {
                    throw new Exception("no $line_execute found.");
                }
                
                $interface = 'Magento\Framework\Event\ObserverInterface';
                if(!$r->implementsInterface($interface))
                {
                    throw new Exception($class . ' does not implement observer interface' );
                }
                
                $output->writeln("Finished $class");
            }
        }
        $output->writeln("Hello World");  
    }
} 