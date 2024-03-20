<?php declare(strict_types=1);

namespace EW\Core\Plugin;

use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Quote\Model\Quote\Address;

class ConvertQuoteToOrderAddress
{
    /**
     * @param ToOrderAddress $subject
     * @param OrderAddressInterface $result
     * @param Address $address
     * @return OrderAddressInterface
     */
    public function afterConvert(
        ToOrderAddress $subject,
        OrderAddressInterface $result,
        Address $address
    ): OrderAddressInterface
    {
        if ($addressClassification = $address->getData('purpose')) {
            $result->setData('purpose', $addressClassification);
        }

        return $result;
    }
}
