<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;


class OrderTracking extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $labels = @json_decode($row->getLabelNumber(), true);
        if (!is_array($labels)){
            return '';
        }
        return '<a href="#" class="action-default scalable action-save action-secondary venipak-order-tracking" data-id = "'.$row->getId().'" >'.__('Shipment tracking').'</a>';
    }

}
