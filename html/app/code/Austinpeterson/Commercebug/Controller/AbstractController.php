<?php
namespace Austinpeterson\Commercebug\Controller;

use Austinpeterson\Commercebug\Model\ViewVars;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use ReflectionClass;

abstract class AbstractController extends \Magento\Framework\App\Action\Action
{
    protected $developerHelperData;

    protected $objectManagerConfig;
    protected $eventManager;
    protected $config;    
    
    public function __construct(
        Context $context, ScopeInterface $config, 
        //ManagerInterface $eventManager, ObjectManagerInterface $objectManager,
        PageFactory $resultPageFactory, 
        ViewVars $viewVars,
        \Magento\Framework\ObjectManager\ConfigInterface $objectManagerConfig,
        \Magento\Developer\Helper\Data $developerHelperData)
    {
        $this->developerHelperData  = $developerHelperData;
        if(!$this->developerHelperData->isDevAllowed())
        {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        $this->config               = $config;
        $this->eventManager         = $context->getEventManager();        
        $this->objectManager        = $context->getObjectManager(); 
        $this->resultPageFactory    = $resultPageFactory;
        $this->viewVars             = $viewVars;
        $this->objectManagerConfig  = $objectManagerConfig;
        return parent::__construct($context);
    }

    protected function shouldSkipInstantiation($class)
    {
        if($class === 'Magento\Framework\App\Bootstrap')
        {
            return true;            
        }
        
        $preference = $this->objectManagerConfig->getPreference($class);
        $r = new ReflectionClass($preference);
        if($r->isAbstract())
        {
            return true;
        }

        return false;
    }
}

