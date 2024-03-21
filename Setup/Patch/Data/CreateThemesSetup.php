<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Theme\Model\ThemeFactory;
use Magento\Theme\Model\ResourceModel\Theme as ThemeResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Psr\Log\LoggerInterface;

class CreateThemesSetup extends AbstractDataPatch
{
    const THEME_ONE_CODE = 'EW/one';
    const THEME_TWO_CODE = 'EW/two';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ThemeCollectionFactory $themeCollectionFactory
     * @param ThemeFactory $themeFactory
     * @param ThemeResource $themeResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected ThemeCollectionFactory $themeCollectionFactory,
        protected ThemeFactory $themeFactory,
        protected ThemeResource $themeResource,
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

        $themeOneCollection = $this->themeCollectionFactory->create()->addFieldToFilter('code', self::THEME_ONE_CODE);
        $themeTwoCollection = $this->themeCollectionFactory->create()->addFieldToFilter('code', self::THEME_TWO_CODE);

        if (!$themeOneCollection->getSize()) {
            try {
                $blankTheme = $this->themeCollectionFactory->create()
                    ->getThemeByFullPath('frontend/Magento/blank');

                $theme = $this->themeFactory->create();
                $theme->setData([
                    'area' => 'frontend',
                    'theme_path' => self::THEME_ONE_CODE,
                    'theme_title' => 'One Theme',
                    'is_featured' => 0,
                    'parent_id' => $blankTheme->getId(),
                    'code' => self::THEME_ONE_CODE,
                ]);

                $this->themeResource->save($theme);
            } catch (AlreadyExistsException|\Exception $e) {
                $this->logger->error('Error on "' . self::THEME_ONE_CODE . '" saving: ' . $e->getMessage());
            }
        }

        if (!$themeTwoCollection->getSize()) {
            try {
                $themeOne = $this->themeCollectionFactory->create()
                    ->getThemeByFullPath('frontend/' . self::THEME_ONE_CODE);

                $theme = $this->themeFactory->create();
                $theme->setData([
                    'area' => 'frontend',
                    'theme_path' => self::THEME_TWO_CODE,
                    'theme_title' => 'Two Theme',
                    'is_featured' => 0,
                    'parent_id' => $themeOne->getId(),
                    'code' => self::THEME_TWO_CODE,
                ]);

                $this->themeResource->save($theme);
            } catch (AlreadyExistsException|\Exception $e) {
                $this->logger->error('Error on "' . self::THEME_TWO_CODE . '" saving: ' . $e->getMessage());
            }
        }

        $this->moduleDataSetup->endSetup();
    }
}
