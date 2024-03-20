<?php declare(strict_types=1);

namespace EW\Core\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;

class SaveShippingAddressExtensionAttributes
{
    /**
     * @param ShippingInformationManagementInterface $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): void
    {
        $shippingAddress = $addressInformation->getShippingAddress();

        if ($extensionAttributes = $shippingAddress->getExtensionAttributes()) {
            $shippingAddress->setData('purpose', $extensionAttributes->getPurpose());
        }
    }
}
