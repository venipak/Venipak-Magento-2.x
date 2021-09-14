<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Fedex method source implementation
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Mijora\Venipak\Model\Source;

class LabelSize extends \Mijora\Venipak\Model\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'label_size';
}
