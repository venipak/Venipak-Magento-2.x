<?php

namespace Mijora\Venipak\Block\Adminhtml\Order;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;

// also you can use Magento Default CollectionFactory
class Grid extends Extended {

    protected $registry;
    protected $_objectManager = null;
    protected $orderFactory;

    public function __construct(
            Context $context,
            Data $backendHelper,
            Registry $registry,
            ObjectManagerInterface $objectManager,
            \Mijora\Venipak\Model\OrderFactory $orderFactory,
            array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareMassaction() {
        
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
                'print',
                [
                    'label' => __('Print labels'),
                    'url' => $this->getUrl('mijora_venipak/order/label')
                ]
        );

        return $this;
    }

    protected function _prepareCollection() {
        $venipakOrders = $this->orderFactory->create()->getCollection()
                        ->addFieldToSelect('*')->addExpressionFieldToSelect(
                'fullname',
                "CONCAT_WS(' ', customer.firstname, customer.lastname)",
                []
        );
        $venipakOrders->addFieldToFilter('id', array('neq' => ''));
        $venipakOrders->getSelect()->joinLeft(
                ['order' => $venipakOrders->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                ['order.id' => 'increment_id']
        )->join(
                ['customer' => $venipakOrders->getTable('sales_order_address')],
                'customer.parent_id = order.entity_id AND customer.address_type = "shipping"',
                ['customer.firstname' => 'firstname', 'customer.lastname' => 'lastname'],
        );

        $this->setCollection($venipakOrders);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn(
                'select',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'id',
                    'align' => 'center',
                    'index' => 'id',
                ]
        );
        $this->addColumn(
                'id',
                [
                    'header' => __('ID'),
                    'type' => 'number',
                    'index' => 'id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'order.entity_id',
                [
                    'header' => __('Order ID'),
                    'type' => 'text',
                    'index' => 'order.id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'fullname',
                [
                    'header' => __('Fullname'),
                    'type' => 'text',
                    'index' => 'fullname',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'labels',
                [
                    'header' => __('Label numbers'),
                    'index' => 'label_number',
                    'type' => 'text',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\Labels',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'created_at',
                [
                    'header' => __('Created At'),
                    'index' => 'created_at',
                    'type' => 'datetime',
                ]
        );
        $this->addColumn(
                'action.print',
                [
                    'header' => '',
                    'index' => 'id',
                    'type' => 'text',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\PrintLabels',
                ]
        );
        $this->addColumn(
                'action.generate',
                [
                    'header' => '',
                    'index' => 'id',
                    'type' => 'text',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\GenerateLabels',
                ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/actionName', ['_current' => true]);
    }

}
