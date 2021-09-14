<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mijora\Venipak\Model\Source;

/**
 * Fedex freemethod source implementation
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Freemethod extends \Mijora\Venipak\Model\Source\Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, ['value' => '', 'label' => __('None')]);
        return $arr;
    }
}
