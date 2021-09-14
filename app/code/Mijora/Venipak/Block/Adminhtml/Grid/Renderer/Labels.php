<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class Labels extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if (!$row->getLabelNumber()){
            return '-';
        }
        $labels = @json_decode($row->getLabelNumber(), true);
        if (is_array($labels)){
            return implode(', ', $labels);
        }
        return '-';
    }

}
