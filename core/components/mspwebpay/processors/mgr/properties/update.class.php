<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

include_once 'base.class.php';

/**
 * Class mspWebPayPaymentPropertiesUpdateProcessor
 */
class mspWebPayPaymentPropertiesUpdateProcessor extends mspWebPayPaymentPropertiesBaseProcessor
{
    public function process()
    {
        $properties = $this->getPaymentProperties();

        $key = $this->getProperty(self::PROPERTY_KEY);
        $value = $this->getProperty(self::PROPERTY_VALUE);

        $properties[$key] = $value; // overwrite

        return $this->savePaymentProperties($properties)
            ? $this->success()
            : $this->failure($this->modx->lexicon('ms2_payment_webpay_save_props_err'));
    }
}

return mspWebPayPaymentPropertiesUpdateProcessor::class;
