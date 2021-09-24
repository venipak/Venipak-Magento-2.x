<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class WarehouseDefault extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if ($row->getDefault()){
            return __('Yes');
        } 
        return __('No');
    }

}
