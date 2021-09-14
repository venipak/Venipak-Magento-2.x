<?php

namespace Mijora\Venipak\Controller\Frontend;

class Front extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $venipakOrderFactory;
    protected $orderFactory;
    protected $coreRegistry;
    protected $json;
    protected $api;
    protected $quoteFactory;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Controller\Result\JsonFactory $json,
            \Mijora\Venipak\Model\Helper\MjvpApi $api,
            \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->orderFactory = $orderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->json = $json;
        $this->api = $api;
        $this->quoteFactory = $quoteFactory;
    }

    public function execute() {

        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = $this->quoteFactory->create();
        $quote->load($quoteId);

        $filter_key = $this->getRequest()->getParam('filter_key');
        $filtered_terminals = $this->getFilteredTerminals($filter_key, $quote);
        die(json_encode(['mjvp_terminals' => $filtered_terminals]));
    }

    private function getFilteredTerminals($filter = '', $entity = null) {
        if ($entity->getId()) {
            $country_code = $entity->getShippingAddress()->getCountryId();

            $all_terminals_info = $this->api->getTerminals($country_code);
            $filtered_terminals = $this->filterTerminalsByWeight($all_terminals_info, $entity);

            $filtered_terminals = array_values($filtered_terminals);
            if (!$filter)
                return $filtered_terminals;

            $terminals = $filtered_terminals;
            $terminal_field = 'type';
            $value = 0;
            if ($filter == 'pickup') {
                $value = 1;
            } elseif ($filter == 'locker') {
                $value = 3;
            } elseif ($filter == 'cod') {
                $terminal_field = 'cod_enabled';
                $value = 1;
            }

            foreach ($terminals as $key => $terminal) {
                if (isset($terminal->$terminal_field) && $terminal->$terminal_field != $value)
                    unset($terminals[$key]);
            }
            $terminals = array_values($terminals);
            return $terminals;
        } else {
            return [];
        }
    }

    private function filterTerminalsByWeight($terminals, $entity) {
        $items = $entity->getAllItems();

        $weight = 0;
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        foreach ($terminals as $key => $terminal) {
            if (isset($terminal->size_limit) && $terminal->size_limit < $weight)
                unset($terminals[$key]);
        }
        return $terminals;
    }

}
