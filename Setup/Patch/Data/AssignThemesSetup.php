<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class AssignThemesSetup extends AbstractDataPatch
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     * @param ThemeCollectionFactory $themeCollectionFactory
     * @param WriterInterface $configWriter
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected StoreManagerInterface $storeManager,
        protected ThemeCollectionFactory $themeCollectionFactory,
        protected WriterInterface $configWriter
    ) {
        parent::__construct($moduleDataSetup);
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $themeOne = $this->themeCollectionFactory->create()->getThemeByFullPath('frontend/EW/one');
        $themeTwo = $this->themeCollectionFactory->create()->getThemeByFullPath('frontend/EW/two');

        // Assign theme one globally (default scope)
        if ($themeOneId = $themeOne->getId()) {
            $this->configWriter->save('design/theme/theme_id', $themeOneId);
        }

        // Assign theme two to store view
        if ($themeTwoId = $themeTwo->getId()) {
            $storeViews = $this->storeManager->getStores();

            foreach ($storeViews as $storeView) {
                // Set theme only for desired store view
                if ($storeView->getCode() == MainWebsiteAndStoreSetup::AT_DE_STORE_VIEW_CODE) {
                    $this->configWriter->save(
                        'design/theme/theme_id',
                        $themeTwoId,
                        ScopeInterface::SCOPE_STORES,
                        $storeView->getId()
                    );
                }
            }
        }

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [MainWebsiteAndStoreSetup::class];
    }
}
