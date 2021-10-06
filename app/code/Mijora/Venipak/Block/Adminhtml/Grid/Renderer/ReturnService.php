<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class ReturnService extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if ($this->getColumn()->getValue()){
            return __('Yes');
        } 
        return __('No');
    }

}
