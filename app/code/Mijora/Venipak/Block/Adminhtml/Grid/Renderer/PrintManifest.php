<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class PrintManifest extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $label = __("Print manifest");
        if (!$row->getIsClosed()) {
            $label = __("Close and print manifest");
        }
        return '<a href="' . $this->getUrl('mijora_venipak/manifest/printmanifest', ['id' => $row->getManifestId()]) . '" class="action-default scalable action-save action-primary" target ="_blank">' . $label . '</a>';
     
    }

}
