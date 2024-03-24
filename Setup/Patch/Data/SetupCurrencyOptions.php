<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CurrencySymbol\Model\System\Currencysymbol;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class SetupCurrencyOptions extends AbstractDataPatch
{
    const XML_PATH_CURRENCY_SYMBOL_POSITION = 'currency/options/symbol_position';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param WriterInterface $configWriter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected ScopeConfigInterface $scopeConfig,
        protected StoreManagerInterface $storeManager,
        protected SerializerInterface $serializer,
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

        // Set currency symbol and preserving any other previously set currency symbols
        $currentSymbolConfig = $this->scopeConfig->getValue(Currencysymbol::XML_PATH_CUSTOM_CURRENCY_SYMBOL);
        $unserializedSymbolConfig = $currentSymbolConfig ? $this->serializer->unserialize($currentSymbolConfig) : [];
        $customCurrencySymbolConfig = is_array($unserializedSymbolConfig) ? $unserializedSymbolConfig : [];
        // Set EUR symbol to add space paddings
        $customCurrencySymbolConfig['EUR'] = " \u{20ac} ";
        // Save currency custom symbol config globally (default scope)
        $this->configWriter->save(
            Currencysymbol::XML_PATH_CUSTOM_CURRENCY_SYMBOL,
            $this->serializer->serialize($customCurrencySymbolConfig)
        );

        try {
            // Set currency symbol position to right value for website scope
            $defaultWebsite = $this->storeManager->getWebsite();
            $this->configWriter->save(
                self::XML_PATH_CURRENCY_SYMBOL_POSITION,
                1,
                ScopeInterface::SCOPE_WEBSITES,
                $defaultWebsite->getId()
            );
        } catch (LocalizedException $e) {
            $this->logger->error('Error on default website retrieval: ' . $e->getMessage());
        }

        $this->moduleDataSetup->endSetup();
    }
}
