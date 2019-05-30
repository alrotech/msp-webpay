<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

include_once 'base.class.php';

/**
 * Class mspWebPayPaymentPropertiesDeleteProcessor
 */
class mspWebPayPaymentPropertiesDeleteProcessor extends mspWebPayPaymentPropertiesBaseProcessor
{
    public function process()
    {
        $properties = $this->getPaymentProperties();

        $key = $this->getProperty(self::PROPERTY_KEY);

        if (!array_key_exists($key, $properties) && $key !== 'all') {
            $this->failure('ms2_payment_webpay_props_key_nf');
        }

        if ($key === 'all') {
            $properties = [];
        } else {
            unset($properties[$key]);
        }

        return $this->savePaymentProperties($properties)
            ? $this->success()
            : $this->failure($this->modx->lexicon('ms2_payment_webpay_save_props_err'));
    }
}

return mspWebPayPaymentPropertiesDeleteProcessor::class;
