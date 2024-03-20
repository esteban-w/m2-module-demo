<?php declare(strict_types=1);

namespace EW\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ActionInterface;
use Magento\Checkout\Helper\Cart as CartHelper;
use Psr\Log\LoggerInterface;

class FeaturedProducts implements ArgumentInterface
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ImageFactory $imageFactory
     * @param CartHelper $cartHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected ImageFactory $imageFactory,
        protected CartHelper $cartHelper,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @return array
     */
    public function getFeaturedProducts(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_featured', 1)
            ->create();

        try {
            $products = $this->productRepository->getList($searchCriteria);
            return $products->getItems();
        } catch (LocalizedException $e) {
            $this->logger->info('Error when getting featured products: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return ImageBlock
     */
    public function getProductImage(
        Product $product,
        string $imageId,
        array $attributes = []
    ): ImageBlock
    {
        return $this->imageFactory->create($product, $imageId, $attributes);
    }

    /**
     * @param Template $block
     * @param Product $product
     * @return string
     */
    public function getProductPriceHtml(Template $block, Product $product): string
    {
        $priceRender = $this->getPriceRender($block);

        if ($priceRender) {
            return $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'zone' => PricingRender::ZONE_ITEM_LIST
                ]
            );
        }

        return '';
    }

    /**
     * @param Template $block
     * @return bool|BlockInterface
     */
    protected function getPriceRender(Template $block): bool|BlockInterface
    {
        $priceRender = false;

        try {
            $priceRender = $block->getLayout()->getBlock('product.price.render.default');
        } catch (LocalizedException $e) {
        }

        return $priceRender;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAddToCartData(Product $product): array
    {
        return [
            'action' => $this->getAddToCartUrl($product),
            ActionInterface::PARAM_NAME_URL_ENCODED => $this->getCurrentUrlEncoded()
        ];
    }

    /**
     * @param Product $product
     * @return string
     */
    protected function getAddToCartUrl(Product $product): string
    {
        return $this->cartHelper->getAddUrl($product);
    }

    /**
     * @return string
     */
    protected function getCurrentUrlEncoded(): string
    {
        return $this->cartHelper->getEncodedUrl();
    }
}
