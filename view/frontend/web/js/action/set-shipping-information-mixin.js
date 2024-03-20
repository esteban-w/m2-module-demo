define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function (wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            // Get shipping information
            const shippingAddress = quote.shippingAddress();

            shippingAddress.extension_attributes = shippingAddress.extension_attributes || {};

            // Get value from custom attributes
            const attributeCode = 'purpose';
            const purposeAttribute = shippingAddress.customAttributes.find((element) =>
                element.attribute_code === attributeCode
            );

            // Set extension attribute
            if (purposeAttribute) {
                shippingAddress.extension_attributes[attributeCode] = purposeAttribute.value;
            }

            return originalAction();
        });
    };
});
