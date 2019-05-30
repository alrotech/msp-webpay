<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

include_once 'base.class.php';

/**
 * Class mspWebPayPaymentPropertiesGetListProcessor
 */
class mspWebPayPaymentPropertiesGetListProcessor extends mspWebPayPaymentPropertiesBaseProcessor
{
    public function process()
    {
        $properties = $this->getPaymentProperties();

        $list = [];
        foreach ($properties as $key => $value) {
            $list[] = ['key' => $key, 'value' => $value];
        }

        $output = array_slice($list, $this->getProperty('start'), $this->getProperty('limit'));

        return $this->outputArray($output, count($list));
    }
}

return mspWebPayPaymentPropertiesGetListProcessor::class;
