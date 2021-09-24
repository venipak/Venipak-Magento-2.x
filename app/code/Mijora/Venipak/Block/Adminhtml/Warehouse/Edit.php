<?php

namespace Mijora\Venipak\Block\Adminhtml\Warehouse;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
            Context $context,
            Registry $registry,
            array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'warehouse_id';
        $this->_controller = 'adminhtml_warehouse';
        $this->_blockGroup = 'Mijora_Venipak';

        parent::_construct();

        //$this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ]
                        ]
                    ]
                ],
                -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText() {
        $posts = $this->_coreRegistry->registry('magetop_blog');
        if ($posts->getId()) {
            $postsTitle = $this->escapeHtml($posts->getTitle());
            return __("Edit Warehouse '%1'", $postsTitle);
        } else {
            return __('Add Warehouse');
        }
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout() {


        return parent::_prepareLayout();
    }

}
