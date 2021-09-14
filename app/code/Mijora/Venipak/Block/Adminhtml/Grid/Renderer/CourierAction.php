<?php
namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class CourierAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
    * Renders grid column
    * @param \Magento\Framework\DataObject $row
    * @return string
    */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getArrivalDateFrom() && $row->getArrivalDateTo()){
            return '';
        }
        return '<a href="#" class="action-default scalable action-save action-primary call_courrier" data-warehouse = "'.$row->getWarehouseId().'" data-id = "'.$row->getManifestId().'" >'.__('Call courier').'</a>';
    }
}