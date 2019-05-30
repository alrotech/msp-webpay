<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/**
 * Class mspWebPayPaymentPropertiesBaseProcessor
 */
class mspWebPayPaymentPropertiesBaseProcessor extends modProcessor
{
    const PROPERTY_PAYMENT = 'payment';
    const PROPERTY_KEY = 'key';
    const PROPERTY_VALUE = 'value';

    /** @var msPayment */
    protected $payment;

    /**
     * @return msPayment|object
     */
    protected function getPayment()
    {
        if (!$this->payment) {
            $this->payment = $this->modx->getObject('msPayment', $this->getProperty(self::PROPERTY_PAYMENT));
        }

        return $this->payment;
    }

    /**
     * @return mixed
     */
    protected function getPaymentProperties()
    {
        return $this->getPayment()->get('properties');
    }

    /**
     * @param array $properties
     * @return bool
     */
    protected function savePaymentProperties(array $properties = [])
    {
        $this->payment->set('properties', $properties);

        return $this->payment->save();
    }

    public function process() {}
}
