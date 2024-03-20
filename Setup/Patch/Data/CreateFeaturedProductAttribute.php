<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\ValidateException;
use Psr\Log\LoggerInterface;

class CreateFeaturedProductAttribute extends AbstractDataPatch
{
    const ATTRIBUTE_CODE = 'is_featured';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected EavSetupFactory $eavSetupFactory,
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

        $eavSetup =$this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        $defaultAttributeGroupId = $eavSetup->getDefaultAttributeGroupId(
            Product::ENTITY,
            $eavSetup->getDefaultAttributeSetId(Product::ENTITY)
        );

        try {
            $eavSetup->addAttribute(
                Product::ENTITY,
                self::ATTRIBUTE_CODE,
                [
                    'type' => 'int',
                    'label' => 'Featured',
                    'input' => 'boolean',
                    'source' => Boolean::class,
                    'default' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'group' => $defaultAttributeGroupId,
                    'comparable' => true,
                    'is_filterable_in_grid' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'position' => 100,
                    'required' => false,
                    'searchable' => false,
                    'sort_order' => 100,
                    'system' => false,
                    'used_for_promo_rules' => true,
                    'used_for_sort_by' => false,
                    'used_in_product_listing' => false,
                    'user_defined' => true,
                    'visible' => true,
                    'visible_in_advanced_search' => true,
                ]
            );
        } catch (LocalizedException $e) {
            $this->logger->error(
                'Error on attribute "' . self::ATTRIBUTE_CODE . '" creation: ' . $e->getMessage()
            );
        } catch (ValidateException $e) {
            $this->logger->error(
                'Validate Error on attribute "' . self::ATTRIBUTE_CODE . '" creation: ' . $e->getMessage()
            );
        }

        $this->moduleDataSetup->endSetup();
    }
}
