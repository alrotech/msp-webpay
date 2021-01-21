<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Ivan Klimchuk <ivan@klimchuk.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Checker for old installer versions of the component
 *
 * @author Ivan Klimchuk <ivan@klimchuk.com>
 * @package mspWebPay
 * @subpackage build
 */

if (!$object->xpdo) {
    return false;
}

if (file_exists($object->xpdo->getOption('minishop2.core_path', null, MODX_CORE_PATH . 'components/minishop2/')
    . 'custom/payment/webpay.class.php'
)) {
    $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'IMPORTANT NOTE!
            The installer found that you use an old version of mspWebPay with old mechanism of registration custom classes for miniShop2. 
            The new version uses a new service registration mechanism and provides new functions but files in the `core/compoments/minishop2/custom/payment` folder have higher priority and prevent to loading proper service class. 
            The installer can not delete these files, since you could make your edits to the payment mechanism, but for the correct work of the new version you should remove or move these files from the `core/compoments/minishop2/custom/payment` folder yourself.');

    return false;
}

return true;
