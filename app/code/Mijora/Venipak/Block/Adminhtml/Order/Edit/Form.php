<?php

namespace Mijora\Venipak\Block\Adminhtml\Order\Edit;

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
        $model = $this->_coreRegistry->registry('venipak_orders');

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
        $form->setFieldNameSuffix('order');
        // new filed

        $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General')]
        );

        if ($model->getOrderId()) {
            $fieldset->addField(
                    'id',
                    'hidden',
                    ['name' => 'id']
            );
        }
        $fieldset->addField(
                'name',
                'text',
                [
                    'name' => 'name',
                    'label' => __('Name'),
                    'title' => __('Name'),
                    'required' => true
                ]
        );
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
