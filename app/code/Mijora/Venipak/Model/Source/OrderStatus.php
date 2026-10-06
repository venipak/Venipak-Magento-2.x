<?php

namespace Mijora\Venipak\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;

/**
 * All order statuses with an empty "disabled" option
 */
class OrderStatus implements OptionSourceInterface
{
    /**
     * @var OrderConfig
     */
    protected $orderConfig;

    /**
     * @param OrderConfig $orderConfig
     */
    public function __construct(OrderConfig $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => '', 'label' => __('-- Disabled --')]];
        foreach ($this->orderConfig->getStatuses() as $code => $label) {
            $options[] = ['value' => $code, 'label' => $label];
        }

        return $options;
    }
}
