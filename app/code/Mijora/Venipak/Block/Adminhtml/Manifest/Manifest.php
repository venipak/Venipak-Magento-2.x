<?php

namespace Mijora\Venipak\Block\Adminhtml\Manifest;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;

// also you can use Magento Default CollectionFactory
class Manifest extends Extended {

    protected $registry;
    protected $_objectManager = null;
    protected $orderFactory;
    protected $manifestFactory;

    public function __construct(
            Context $context,
            Data $backendHelper,
            Registry $registry,
            ObjectManagerInterface $objectManager,
            \Mijora\Venipak\Model\OrderFactory $orderFactory,
            \Mijora\Venipak\Model\ManifestFactory $manifestFactory,
            array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->orderFactory = $orderFactory;
        $this->manifestFactory = $manifestFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('manifest_id');
        $this->setDefaultSort('manifest_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $manifests = $this->manifestFactory->create()->getCollection()
                ->addFieldToSelect('*');
        /*->addExpressionFieldToSelect(
                'arrival',
                "CONCAT_WS(' ', main_table.arrival_date_from, main_table.arrival_date_to)",
                []
        );
        */
        $manifests->addFieldToFilter('manifest_id', array('neq' => ''));

        $manifests->getSelect()->joinLeft(
                ['warehouse' => $manifests->getTable('venipak_warehouse')],
                'warehouse.warehouse_id = main_table.warehouse_id',
                ['warehouse.name' => 'name']
        );



        $this->setCollection($manifests);
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
                    'index' => 'manifest_id',
                ]
        );
        $this->addColumn(
                'id',
                [
                    'header' => __('ID'),
                    'type' => 'number',
                    'index' => 'manifest_id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'order.entity_id',
                [
                    'header' => __('Number'),
                    'type' => 'text',
                    'index' => 'manifest_number',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'warehouse',
                [
                    'header' => __('Warehouse'),
                    'type' => 'text',
                    'index' => 'warehouse.name',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'arrival',
                [
                    'header' => __('Courier arrival'),
                    'index' => 'manifest_id',
                    'type' => 'text',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\CourierArrival',
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
                    'index' => 'manifest_id',
                    'type' => 'text',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\PrintManifest',
                ]
        );
        $this->addColumn(
                'action.call',
                [
                    'header' => '',
                    'width' => '100px',
                    'getter' => 'getId',
                    'renderer' => 'Mijora\Venipak\Block\Adminhtml\Grid\Renderer\CourierAction',
                    'value' => 'a',
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'id',
                    'is_system' => true
                ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/actionName', ['_current' => true]);
    }

}
