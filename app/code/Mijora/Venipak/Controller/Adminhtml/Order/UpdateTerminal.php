<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


class UpdateTerminal extends \Magento\Framework\App\Action\Action {

    protected $orderRepository;
     
    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory, 
        OrderManagementInterface $orderManagement, 
        OrderRepositoryInterface $orderRepository
    )
    {
      $this->orderRepository = $orderRepository;
      parent::__construct($context);
    }
    
    public function execute()
    {
      $order_id = $this->getRequest()->getParam('order_id');
      $terminal = $this->getRequest()->getParam('terminal_id');
      
      $order = $this->orderRepository->get($order_id);
      if ($order){
        $shippingAddress = $order->getShippingAddress();
        $data =  @json_decode($shippingAddress->getVenipakData());
        if (!is_object($data)){
          $data = new \stdClass();
        }
        $data->pickupPoint = $terminal;
        $shippingAddress->setVenipakData(json_encode($data));
        $shippingAddress->save();
        
        $text = __('Parcel terminal updated');
        $this->messageManager->addSuccess($text);
      } else {
        $text = __('Parcel terminal not updated');
        $this->messageManager->addError($text);
      }
      $this->_redirect($this->_redirect->getRefererUrl());
      return;
     
    }

}
