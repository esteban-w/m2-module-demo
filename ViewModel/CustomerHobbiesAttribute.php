<?php declare(strict_types=1);

namespace EW\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use EW\Core\Setup\Patch\Data\CreateHobbiesCustomerAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CustomerHobbiesAttribute implements ArgumentInterface
{
    /**
     * @var AttributeInterface|null
     */
    protected ?AttributeInterface $attribute;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected AttributeRepositoryInterface $attributeRepository,
        protected LoggerInterface $logger,
        protected CustomerRepositoryInterface $customerRepository
    ) {
        try {
            $this->attribute = $this->attributeRepository->get(
                Customer::ENTITY,
                CreateHobbiesCustomerAttribute::ATTRIBUTE_CODE
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->notice(
                'NoSuchEntity error on "' . CreateHobbiesCustomerAttribute::ATTRIBUTE_CODE
                . '" retrieval: ' . $e->getMessage()
            );
            $this->attribute = null;
        }
    }

    /**
     * @return string
     */
    public function getAttributeLabel(): string
    {
        return $this->attribute ? $this->attribute->getDefaultFrontendLabel() : '';
    }

    /**
     * @return string
     */
    public function getAttributeCode(): string
    {
        return $this->attribute ? $this->attribute->getAttributeCode() : '';
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getCustomerAttributeValue($customerId): string
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $customerAttribute = $customer->getCustomAttribute($this->getAttributeCode());
            return $customerAttribute ? $customerAttribute->getValue() : '';
        } catch (NoSuchEntityException|LocalizedException $e) {
            return '';
        }
    }

}
