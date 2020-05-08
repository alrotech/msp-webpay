<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/**
 * Class EncryptedVehicle
 */
class EncryptedVehicle extends xPDOObjectVehicle
{
    const VERSION = '2.0.0';
    const CIPHER = 'AES-256-CBC';

    public $class = self::class;

    /**
     * @param $transport xPDOTransport
     * @param $object
     * @param array $attributes
     */
    public function put(&$transport, &$object, $attributes = array())
    {
        parent::put($transport, $object, $attributes);

        if (defined('PKG_ENCODE_KEY')) {

            $this->payload['object_encrypted'] = $this->encode($this->payload['object'], PKG_ENCODE_KEY);
            unset($this->payload['object']);

            if (isset($this->payload['related_objects'])) {
                $this->payload['related_objects_encrypted'] = $this->encode($this->payload['related_objects'], PKG_ENCODE_KEY);
                unset($this->payload['related_objects']);
            }
        }
    }

    /**
     * @param $transport xPDOTransport
     * @param $options
     *
     * @return bool
     */
    public function install(&$transport, $options)
    {
        if (!$this->decodePayloads($transport)) {
            $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Package can not be decrypted!');
            return false;
        }

        $transport->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package decrypted!');

        return parent::install($transport, $options);
    }

    /**
     * @param $transport xPDOTransport
     * @param $options
     *
     * @return bool
     */
    public function uninstall(&$transport, $options)
    {
        if (!$this->decodePayloads($transport, 'uninstall')) {
            $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Package can not be decrypted!');
            return false;
        }

        $transport->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package decrypted!');

        return parent::uninstall($transport, $options);
    }

    /**
     * @param array $data
     * @param string $key
     *
     * @return string
     */
    protected function encode($data, $key)
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $cipher_raw = openssl_encrypt(serialize($data), self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $cipher_raw);
    }

    /**
     * @param string $string
     * @param string $key
     *
     * @return string
     */
    protected function decode($string, $key)
    {
        $ivLen = openssl_cipher_iv_length(self::CIPHER);
        $encoded = base64_decode($string);

        if (ini_get('mbstring.func_overload')) {
            $strLen = mb_strlen($encoded, '8bit');
            $iv = mb_substr($encoded, 0, $ivLen, '8bit');
            $cipher_raw = mb_substr($encoded, $ivLen, $strLen, '8bit');
        } else {
            $iv = substr($encoded, 0, $ivLen);
            $cipher_raw = substr($encoded, $ivLen);
        }

        return unserialize(openssl_decrypt($cipher_raw, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * @param $transport xPDOTransport
     * @param string $action
     *
     * @return bool
     */
    protected function decodePayloads(&$transport, $action = 'install')
    {
        if (isset($this->payload['object_encrypted']) || isset($this->payload['related_objects_encrypted'])) {
            if (!$key = $this->getDecodeKey($transport, $action)) {
                return false;
            }
            if (isset($this->payload['object_encrypted'])) {
                $this->payload['object'] = $this->decode($this->payload['object_encrypted'], $key);
                unset($this->payload['object_encrypted']);
            }
            if (isset($this->payload['related_objects_encrypted'])) {
                $this->payload['related_objects'] = $this->decode($this->payload['related_objects_encrypted'], $key);
                unset($this->payload['related_objects_encrypted']);
            }
        }

        return true;
    }

    /**
     * @param $transport xPDOTransport
     * @param $action
     *
     * @return bool|string
     */
    protected function getDecodeKey(&$transport, $action)
    {
        $key = false;
        $endpoint = 'package/decode/' . $action;

        /** @var modTransportPackage $package */
        $package = $transport->xpdo->getObject('transport.modTransportPackage', [
            'signature' => $transport->signature
        ]);

        if ($package instanceof modTransportPackage) {
            /** @var modTransportProvider $provider */
            if ($provider = $package->getOne('Provider')) {

                $provider->xpdo->setOption('contentType', 'default');
                $params = array(
                    'package' => $package->package_name,
                    'version' => $transport->version,
                    'username' => $provider->username,
                    'api_key' => $provider->api_key,
                    'vehicle_version' => self::VERSION,
                );

                $response = $provider->request($endpoint, 'POST', $params);

                if ($response->isError()) {
                    $msg = $response->getError();
                    $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, $msg);
                } else {
                    $data = $response->toXml();
                    if ($data->key !== null) {
                        $key = $data->key;
                    } elseif ($data->message !== null) {
                        $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, $data->message);
                    }
                }
            }
        }

        return $key;
    }
}
