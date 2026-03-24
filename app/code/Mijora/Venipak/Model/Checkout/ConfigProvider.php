<?php
namespace Mijora\Venipak\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider implements ConfigProviderInterface
{
    protected $scopeConfig;
    protected $resourceConnection;
    protected $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
    }

    public function getConfig()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $websiteId = $store->getWebsiteId();

        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('core_config_data');

        $value = $this->scopeConfig->getValue(
            'carriers/venipak/title',
            $scope,
            $storeId
        );

        $result = false;

        // 1. Store scope
        $select = $connection->select()
            ->from($table, ['value'])
            ->where('path = ?', 'carriers/venipak/title')
            ->where('scope = ?', 'stores')
            ->where('scope_id = ?', $storeId);

        $result = $connection->fetchOne($select);

        // 2. Website scope
        if ($result === false) {
            $select = $connection->select()
                ->from($table, ['value'])
                ->where('path = ?', 'carriers/venipak/title')
                ->where('scope = ?', 'websites')
                ->where('scope_id = ?', $websiteId);

            $result = $connection->fetchOne($select);
        }

        // 3. Default scope (optional)
        if ($result === false) {
            $select = $connection->select()
                ->from($table, ['value'])
                ->where('path = ?', 'carriers/venipak/title')
                ->where('scope = ?', 'default')
                ->where('scope_id = ?', 0);

            $result = $connection->fetchOne($select);
        }

        $isCustom = $result !== false;

        return [
            'venipak' => [
                'title' => $value,
                'is_custom_title' => $isCustom
            ]
        ];
    }
}