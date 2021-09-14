<?php

namespace Mijora\Venipak\Block\Adminhtml\Warehouse\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic {

    /**
     * @return $this
     */
    /*
      protected function _prepareForm()
      {

      $form = $this->_formFactory->create(
      [
      'data' => [
      'id'    => 'edit_form',
      'action' => $this->getData('action'),
      'method' => 'post',
      'enctype' => 'multipart/form-data'
      ]
      ]
      );
      $form->setUseContainer(true);
      $this->setForm($form);

      return parent::_prepareForm();
      }
     */
    protected function _prepareForm() {
        /** @var $model \Magetop\Helloworld\Model\PostsFactory */
        $model = $this->_coreRegistry->registry('venipak_warehouses');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'edit_form',
                        'action' => $this->getData('action'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                    ]
                ]
        );
        $form->setUseContainer(true);
        //$form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('warehouse');
        // new filed

        $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General')]
        );

        if ($model->getWarehouseId()) {
            $fieldset->addField(
                    'warehouse_id',
                    'hidden',
                    ['name' => 'warehouse_id']
            );
        }
        $fields = [
            'name' => _('Warehouse number'),
            'company_code' => __('Company code'),
            'contact_name' => __('Contact name'),
            'phone' => __('Phone'),
            'address' => _('Address'),
            'city' => _('City'),
            'postcode' => _('Postcode'),
            'country' => _('Country code'),
        ];
        foreach ($fields as $key => $field) {
            $fieldset->addField(
                    $key,
                    'text',
                    [
                        'name' => $key,
                        'label' => $field,
                        'title' => $field,
                        'required' => true
                    ]
            );
        }
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
