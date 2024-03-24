<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AlreadyExistsException;
use Psr\Log\LoggerInterface;

class MainWebsiteAndStoreSetup extends AbstractDataPatch
{
    const AT_DE_STORE_VIEW_CODE = 'at_de';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     * @param StoreFactory $storeFactory
     * @param StoreResource $storeResource
     * @param WriterInterface $configWriter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected StoreManagerInterface $storeManager,
        protected StoreFactory $storeFactory,
        protected StoreResource $storeResource,
        protected WriterInterface $configWriter,
        protected LoggerInterface $logger
    ) {
        parent::__construct($moduleDataSetup);
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        try {
            // Get default values
            $defaultStoreView = $this->storeManager->getDefaultStoreView();
            $defaultStoreGroupId = $defaultStoreView->getStoreGroupId();
            $defaultWebsiteId = $this->storeManager->getWebsite(true)->getId();

            // Configure default Website AT
            $this->setWebsiteConfig($defaultWebsiteId, 'general/country/default', 'AT');
            $this->setWebsiteConfig($defaultWebsiteId, 'currency/options/base', 'EUR');
            $this->setWebsiteConfig($defaultWebsiteId, 'currency/options/default', 'EUR');
            $this->setWebsiteConfig($defaultWebsiteId, 'currency/options/allow', 'EUR');

            // Create a new Store View
            $storeView = $this->storeFactory->create();
            $storeView->setCode(self::AT_DE_STORE_VIEW_CODE);
            $storeView->setName('AT Store View');
            $storeView->setWebsiteId($defaultWebsiteId);
            $storeView->setGroupId($defaultStoreGroupId);
            $storeView->setIsActive(true);
            // Save Store View
            $this->storeResource->save($storeView);

        } catch (AlreadyExistsException $e) {
            $this->logger->error('AlreadyExists Error on store view saving: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Error on website-store_view setting: ' . $e->getMessage());
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $websiteId
     * @param $configPath
     * @param $value
     * @return void
     */
    protected function setWebsiteConfig($websiteId, $configPath, $value): void
    {
        try {
            $this->configWriter->save($configPath, $value, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        } catch (LocalizedException $e) {
            $this->logger->error('Error on website config setting: ' . $e->getMessage());
        }
    }
}
