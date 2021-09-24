<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class GenerateLabels extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if (!$row->getLabelNumber() && !is_object(json_decode($row->getLabelNumber()) )) {
            return '<a href = "#" data-href="' . $this->getUrl('mijora_venipak/order/buildshipmentajax', ['venipak_order_id' => $row->getId()]) . '" class="action-default scalable action-save action-primary generate_labels">' . __('Generate labels') . '</a>';
        }
        return '';
    }

}
