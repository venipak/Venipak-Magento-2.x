<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class PrintLabels extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if (!$row->getLabelNumber()) {
            return '';
        }
        $labels = @json_decode($row->getLabelNumber(), true);
        if (is_array($labels)) {
            return '<a href="' . $this->getUrl('mijora_venipak/order/label', ['id' => $row->getId()]) . '" class="action-default scalable action-save action-primary call_courrier" target ="_blank">' . __('Print labels') . '</a>';
        }
        return '';
    }

}
