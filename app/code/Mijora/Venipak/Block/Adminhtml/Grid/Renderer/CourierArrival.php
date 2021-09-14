<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

class CourierArrival extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if (!$row->getArrivalDateFrom() || !$row->getArrivalDateTo()){
            return '';
        }
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $from = new \DateTime($row->getArrivalDateFrom());
        $to = new \DateTime($row->getArrivalDateTo());
        
        $fromDiff = $today->diff($from);
        $toDiff = $today->diff($to);
        if ($fromDiff->days == 0 || $toDiff->days == 0){
            return $from->format('Y-m-d H:i') . ' - ' . $to->format('Y-m-d H:i');
        }
        return '';
    }

}
