<?php declare(strict_types=1);

namespace EW\Core\Block\Checkout\LayoutProcessor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class AddressPurposeAttribute implements LayoutProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function process($jsLayout): array
    {
        $attributeCode = 'purpose';
        $attributeData = &$jsLayout['components']['checkout']['children']
        ['steps']['children']
        ['shipping-step']['children']
        ['shippingAddress']['children']
        ['shipping-address-fieldset']['children']
        [$attributeCode];

        $attributeData['component'] = 'Magento_Ui/js/form/element/abstract';
        $attributeData['config']['customScope'] = 'shippingAddress.custom_attributes';
        $attributeData['config']['template'] = 'ui/form/field';
        $attributeData['config']['elementTmpl'] = 'ui/form/element/input';
        $attributeData['dataScope'] = "shippingAddress.custom_attributes.$attributeCode";
        $attributeData['label'] = __('Purpose');
        $attributeData['provider'] = 'checkoutProvider';
        $attributeData['validation'] = [
            'required-entry' => true,
            'validate-alpha' => true,
        ];

        return $jsLayout;
    }
}
