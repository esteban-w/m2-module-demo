<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Validator\ValidateException;
use Psr\Log\LoggerInterface;

class CreateHobbiesCustomerAttribute extends AbstractDataPatch
{
    const ATTRIBUTE_CODE = 'hobbies';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param EavConfig $eavConfig
     * @param AttributeResource $attributeResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected EavSetupFactory $eavSetupFactory,
        protected EavConfig $eavConfig,
        protected AttributeResource $attributeResource,
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

        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        try {
            $eavSetup->addAttribute(
                Customer::ENTITY,
                self::ATTRIBUTE_CODE,
                [
                    'type' => 'varchar',
                    'label' => 'Hobbies',
                    'input' => 'text',
                    'default' => '',
                    'frontend_class' => 'validate-length maximum-length-255',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => true,
                    'position' => 150,
                    'required' => false,
                    'sort_order' => 150,
                    'system' => false,
                    'user_defined' => true,
                    'visible' => true,
                ]
            );

            // Add attribute to default attribute set and group
            $eavSetup->addAttributeToSet(
                Customer::ENTITY,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                self::ATTRIBUTE_CODE
            );

            // Add attribute to customer forms
            $attribute = $this->eavConfig->getAttribute(
                Customer::ENTITY,
                self::ATTRIBUTE_CODE
            );
            $attribute->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit',
            ]);

            $this->attributeResource->save($attribute);

        } catch (LocalizedException $e) {
            $this->logger->error(
                'Error on attribute "' . self::ATTRIBUTE_CODE . '" creation: ' . $e->getMessage()
            );
        } catch (ValidateException $e) {
            $this->logger->error(
                'Validate Error on attribute "' . self::ATTRIBUTE_CODE . '" creation: ' . $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->error(
                'Error on "' . self::ATTRIBUTE_CODE . '" update: ' . $e->getMessage()
            );
        }

        $this->moduleDataSetup->endSetup();
    }
}
