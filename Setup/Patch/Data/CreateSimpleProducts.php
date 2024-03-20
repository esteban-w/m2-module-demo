<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch\Data;

use EW\Core\Setup\Patch\AbstractDataPatch;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\ClassModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Psr\Log\LoggerInterface;

class CreateSimpleProducts extends AbstractDataPatch
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param File $io
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param TaxClassRepositoryInterface $taxClassRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        protected WebsiteRepositoryInterface $websiteRepository,
        protected StoreManagerInterface $storeManager,
        protected Filesystem $filesystem,
        protected File $io,
        protected ProductFactory $productFactory,
        protected ProductRepositoryInterface $productRepository,
        protected LoggerInterface $logger,
        protected TaxClassRepositoryInterface $taxClassRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected State $state
    ) {
        parent::__construct($moduleDataSetup);
    }

    public static function getDependencies(): array
    {
        return [CreateFeaturedProductAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        // Retrieve all websites
        $websites = $this->websiteRepository->getList();
        $websiteIds = [];

        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        // Retrieve all root categories
        $stores = $this->storeManager->getStores();
        $rootCategoryIds = [];

        try {
            foreach ($stores as $store) {
                $rootCategoryIds[] = $store->getRootCategoryId();
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error(
                'No Category Found Error on store root category retrieval: ' . $e->getMessage()
            );
        }

        // Retrieve product tax class
        try {
            $productTaxClassId = null;
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('class_type', ClassModel::TAX_CLASS_TYPE_PRODUCT)
                ->addFilter('class_name', 'Taxable Goods')
                ->create();
            $taxClasses = $this->taxClassRepository->getList($searchCriteria);
            $items = $taxClasses->getItems();

            foreach ($items as $taxClass) {
                $productTaxClassId = $taxClass->getClassId();
            }
        } catch (InputException $e) {
            $this->logger->error('Input Error on product tax class retrieval: ' . $e->getMessage());
        }


        // Check if the 'pub/media/import' temporary directory exists and create it if it doesn't
        $mediaImportDir = 'import';
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaImportPath = $mediaDirectory->getAbsolutePath($mediaImportDir);
        if (!$mediaDirectory->isExist($mediaImportDir)) {
            $this->io->mkdir($mediaImportPath);
        }

        $simpleProducts = [
            [
                'sku' => 'beoplay_a8',
                'name' => 'Beoplay A9',
                'url_key' => 'beoplay-a8',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A9',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
                CreateFeaturedProductAttribute::ATTRIBUTE_CODE => 1,
            ],
            [
                'sku' => 'beoplay_a9',
                'name' => 'Beoplay A9',
                'url_key' => 'beoplay-a9',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A9',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
                CreateFeaturedProductAttribute::ATTRIBUTE_CODE => 1,
            ],
            [
                'sku' => 'beoplay_a10',
                'name' => 'Beoplay A9',
                'url_key' => 'beoplay-a10',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A9',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
                CreateFeaturedProductAttribute::ATTRIBUTE_CODE => 1,
            ],
            [
                'sku' => 'beoplay_a11',
                'name' => 'Beoplay A9',
                'url_key' => 'beoplay-a11',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A9',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
                CreateFeaturedProductAttribute::ATTRIBUTE_CODE => 1,
            ],
            [
                'sku' => 'beoplay_a12',
                'name' => 'Beoplay A12',
                'url_key' => 'beoplay-a12',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A12',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
            ],
            [
                'sku' => 'beoplay_a13',
                'name' => 'Beoplay A13',
                'url_key' => 'beoplay-a13',
                'status' => Status::STATUS_ENABLED,
                'visibility'=> Visibility::VISIBILITY_BOTH,
                'price' => 499,
                'type_id' => Type::TYPE_SIMPLE,
                'description' => 'Classic multiroom speaker with 480 watts of powerful sound, and a lot more features.',
                'short_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'meta_title' => 'Beoplay A13',
                'meta_keyword' => 'Speaker, Sound',
                'meta_description' => 'Classic multiroom speaker with 480 watts of powerful sound.',
                'stock_data' => [
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                ],
                'website_ids' => $websiteIds,
                'category_ids' => $rootCategoryIds,
            ],
        ];

        // Product repository's "save()" method depends on an area code being set
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            $this->logger->info('Attempting to set the area code when is already set: ' . $e->getMessage());
        }

        try {
            foreach ($simpleProducts as $productData) {
                $product = $this->productFactory->create();
                $product->setData($productData);
                // Get and set default attribute set
                $product->setAttributeSetId($product->getDefaultAttributeSetId());
                // Set tax class if it's available
                if ($productTaxClassId) {
                    $product->setData('tax_class_id', $productTaxClassId);
                }

                // Copy the image from the module's directory to the pub/media/import directory
                $imageName = $productData['sku'] . '.png';
                $imageTempPath = $mediaImportDir . '/' . $imageName;
                $imageAbsoluteTempPath = $mediaDirectory->getAbsolutePath($imageTempPath);
                if (!file_exists($imageAbsoluteTempPath)) {
                    copy(__DIR__ . '/images/' . $imageName, $imageAbsoluteTempPath);
                }

                // Add product image to product
                $product->addImageToMediaGallery(
                    $imageTempPath,
                    ['image', 'small_image', 'thumbnail'],
                    true,
                    false
                );

                $this->productRepository->save($product);
            }
        } catch (CouldNotSaveException $e) {
            $this->logger->error('Save Error on product save operation: ' . $e->getMessage());
        } catch (InputException $e) {
            $this->logger->error('Input Error on product save operation: ' . $e->getMessage());
        } catch (StateException $e) {
            $this->logger->error('State Error on product save operation: ' . $e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->error('Localized Error on product save operation: ' . $e->getMessage());
        }

        $this->moduleDataSetup->endSetup();
    }
}
