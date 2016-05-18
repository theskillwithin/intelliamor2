<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Observers;

class Add extends AbstractObserver
{
    protected $logger;
    protected $pulsestormCommercebugLogFactory;
    protected $renderedData;
    protected $logFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Austinpeterson\Commercebug\Model\LogFactory $logFactory,
        \Austinpeterson\Commercebug\Model\RenderedData $renderedData,        
        \Magento\Developer\Helper\Data $developerHelper        
    )
    {
        $this->developerHelper = $developerHelper;
        $this->logFactory = $logFactory;
        $this->pulsestormCommercebugLogFactory = $logFactory;
        $this->logger = $logger;
        $this->renderedData = $renderedData;
        return parent::__construct($developerHelper);
    }
    
    protected function _execute(\Magento\Framework\Event\Observer $observer)
    {
        return $this->addToHtmlPage($observer);
    }
    
    protected function getCommerceBugDataFromLog($request)
    {
        $id = $request->getParam('id');
        $log = $this->logFactory->create()->load($id);
        $array = json_decode($log->getJsonLog(), true);
        
        return $array;
    }
    
    protected function isDirectLogAccess($request)
    {
        return strpos($request->getOriginalPathInfo(), 'pulsestorm_commercebug/viewlog') !== false;
    }
    
    protected function getCommerceBugData($observer)
    {
        $request = $observer->getRequest();
        if($this->isDirectLogAccess($request))
        {
            return $this->getCommerceBugDataFromLog($request);
        }

        $cb_data = \Austinpeterson\Commercebug\Model\All::asData();
        $cb_data['server']  = $_SERVER;
        
        //add to the renderData singleton in case we need/want to access
        //the data somewhere else
        $rendered_data      = $this->renderedData->setData($cb_data);
        return $cb_data;
    }
    
    protected function logData($data, $observer)
    {
        $request = $observer->getRequest();
        if($this->isDirectLogAccess($request))
        {
            return;
        }
        $model = $this->pulsestormCommercebugLogFactory->create()
            ->logData($data);    
    }
    
    protected function renderScriptTag($data)
    {
        $renderer = new \Austinpeterson\Commercebug\Renderer\Json;
        $renderer->setData($data);                
        $script   = $renderer->render();
        return $script;  
    }
    
    protected function addScriptTagToPage($script, $observer)
    {
        $response           = $observer->getResponse();
        $body               = $response->getBody();                 
        $new_body           = str_replace('</body>', 
            $script . '</body>',  $body);
        $response->setBody($new_body);     
    }
    
    public function addToHtmlPage($observer)
    {                
        //render the json that's added to page via script tags               
        $cb_data            = $this->getCommerceBugData($observer);                

        //add the data to the rolling log table
        $this->logData($cb_data, $observer);
            
        $header_accept = $observer->getRequest()->getHeader('Accept');
        if(strpos($header_accept, 'text/html') === false)
        {
            return;
        }

        //renders the actual script tag to add to the HTML page
        $script = $this->renderScriptTag($cb_data);
        
        //adds the script tag to the HTML page
        $this->addScriptTagToPage($script, $observer);   
    }
}
