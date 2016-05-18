<?php
namespace Austinpeterson\Commercebug\Plugin\Magento\Framework\View;
class Layout
{
    function beforeGenerateElements($subject){
        \Austinpeterson\Commercebug\Model\All::addTo(
            'request_layout_xml', $subject->getNode()->asXml());
    }
}
