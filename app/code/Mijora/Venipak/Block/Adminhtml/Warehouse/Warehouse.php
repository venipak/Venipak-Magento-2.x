<?php

namespace Mijora\Venipak\Block\Adminhtml\Warehouse;

class Warehouse extends \Magento\Backend\Block\Widget\Grid\Container
{

	protected function _construct()
	{
		$this->_controller = 'adminhtml_warehouse';
		$this->_blockGroup = 'Mijora_Venipak';
		$this->_headerText = __('Warehouses');
		$this->_addButtonLabel = __('Create New warehouse');
		parent::_construct();
	}
}