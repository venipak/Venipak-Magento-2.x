<?php
namespace Mijora\Venipak\Ui\Component\Listing\Columns;

class Action extends \Magento\Backend\Block\Widget\Grid\Column
{

    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name] = 
                html_entity_decode('<button>Test</button>');

            }
        }

        return $dataSource;
    }
}