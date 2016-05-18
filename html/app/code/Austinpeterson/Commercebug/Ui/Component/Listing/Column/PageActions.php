<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
// use Magento\Ui\Component\Listing\Columns\Column;
// use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
// use Magento\Framework\UrlInterface;

/**
 * Class PageActions
 */
class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var UrlInterface */
    protected $urlBuilder;
    protected $viewUrl;
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,        
        array $components = [],
        array $data = []
    ) {        
        $this->context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $id = 'X';
                if(isset($item['pulsestorm_commercebug_log_id']))
                {
                    $id = $item['pulsestorm_commercebug_log_id'];
                }
                $item[$name]['view'] = [
                    'href'=>$this->context->getUrl(
                        'adminhtml/pulsestorm_commercebug/viewlog',['id'=>$id]),
                    'label'=>__('View Log')
                ];
//                 var_dump($name);
//                 exit;
//                 if (isset($item['page_id'])) {
//                     $item[$name]['edit'] = [
//                         'href' => $this->urlBuilder->getUrl($this->editUrl, ['page_id' => $item['page_id']]),
//                         'label' => __('Edit')
//                     ];
//                     $item[$name]['delete'] = [
//                         'href' => $this->urlBuilder->getUrl(self::CMS_URL_PATH_DELETE, ['page_id' => $item['page_id']]),
//                         'label' => __('Delete'),
//                         'confirm' => [
//                             'title' => __('Delete ${ $.$data.title }'),
//                             'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?')
//                         ]
//                     ];
//                 }
//                 if (isset($item['identifier'])) {
//                     $item[$name]['preview'] = [
//                         'href' => $this->actionUrlBuilder->getUrl(
//                             $item['identifier'],
//                             isset($item['_first_store_id']) ? $item['_first_store_id'] : null,
//                             isset($item['store_code']) ? $item['store_code'] : null
//                         ),
//                         'label' => __('Preview')
//                     ];
//                 }
            }
        }

        return $dataSource;
    }
}
