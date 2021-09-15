<?php

namespace Mijora\Venipak\Controller\Frontend;

class Front extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $venipakOrderFactory;
    protected $orderFactory;
    protected $coreRegistry;
    protected $json;
    protected $carrier;
    protected $quoteFactory;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Controller\Result\JsonFactory $json,
            \Mijora\Venipak\Model\Carrier $carrier,
            \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->orderFactory = $orderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->json = $json;
        $this->carrier = $carrier;
        $this->quoteFactory = $quoteFactory;
    }

    public function execute() {

        $country = $this->getRequest()->getParam('country');
        $weight = $this->getRequest()->getParam('weight');
        
        $filter_key = $this->getRequest()->getParam('filter_key');
        $filtered_terminals = $this->getFilteredTerminals($filter_key, $country, $weight);
        die(json_encode(['mjvp_terminals' => $filtered_terminals]));
    }

    private function getFilteredTerminals($filters = '', $country, $weight) {
        
            $all_terminals_info = $this->carrier->getTerminals($country);
            
            $filtered_terminals = $this->filterTerminalsByWeight($all_terminals_info, $weight);

            $filtered_terminals = array_values($filtered_terminals);
            if (!is_array($filters)){
                return [];
            }
            $terminals = $filtered_terminals;
            $terminal_field = 'type';
            $allowed_values = [];
            foreach ($filters as $filter){
                if ($filter == 'pickup') {
                    $allowed_values[] = 1;
                } elseif ($filter == 'locker') {
                    $allowed_values[] = 3;
                } elseif ($filter == 'cod') {
                    $terminal_field = 'cod_enabled';
                    $allowed_values[] = 1;
                }
            }
            
            foreach ($terminals as $key => $terminal) {
                if (isset($terminal->$terminal_field) &&  !in_array($terminal->$terminal_field, $allowed_values))
                    unset($terminals[$key]);
            }
            $terminals = array_values($terminals);
            return $terminals;
       
    }

    private function filterTerminalsByWeight($terminals, $weight) {
        
        foreach ($terminals as $key => $terminal) {
            if (isset($terminal->size_limit) && $terminal->size_limit < $weight)
                unset($terminals[$key]);
        }
        return $terminals;
    }

}
