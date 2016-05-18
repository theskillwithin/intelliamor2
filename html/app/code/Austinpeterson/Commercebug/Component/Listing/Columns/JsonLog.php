<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Component\Listing\Columns;
class JsonLog extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if(!isset($item[$this->getName()]))
                {
                    continue;
                }

                $data = json_decode($item[$this->getName()]);
                if(!isset($data->server))
                {
                    $item[$this->getName()] = 'What Goes Here?';
                    continue;
                }
                $item[$this->getName()] = $data->server->REQUEST_URI;
            }
        }

        return $dataSource;
    }
}
