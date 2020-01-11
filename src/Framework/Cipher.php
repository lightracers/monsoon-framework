<?php
namespace Framework;

use \Exception;

class Cipher
{
    public static function base64EncodeSafe($string)
    {
        $data = base64_encode($string);
        $data = str_replace(
            [
                '+',
                '/',
                '=',
            ],
            [
                '-',
                '_',
                '',
            ],
            $data
        );

        return $data;
    }

    public static function base64DecodeSafe($string)
    {
        $data = str_replace(
            [
                '-',
                '_',
            ],
            [
                '+',
                '/',
            ],
            $string
        );
        $mod4 = (strlen($data) % 4);
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

    public static function encrypt($message, $key = 'DHEbSK1KmU3MQnuhmSQfDIM7adKaBIPMpzWB4GJw')
    {
        if (!$message) {
            return false;
        }

        if (mb_strlen($key, '8bit') < 32) {
            throw new Exception("Needs a 256-bit key!");
        }

        $ivSize  = openssl_cipher_iv_length('aes-256-cbc');
        $ivBytes = openssl_random_pseudo_bytes($ivSize);

        $cipherText = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $ivBytes);

        return trim(self::base64EncodeSafe($ivBytes . $cipherText));
    }

    public static function decrypt($message, $key = 'DHEbSK1KmU3MQnuhmSQfDIM7adKaBIPMpzWB4GJw')
    {
        if (!$message) {
            return false;
        }

        if (mb_strlen($key, '8bit') < 32) {
            throw new Exception("Needs a 256-bit key!");
        }

        $message    = self::base64DecodeSafe($message);
        $ivSize     = openssl_cipher_iv_length('aes-256-cbc');
        $ivString   = mb_substr($message, 0, $ivSize, '8bit');
        $cipherText = mb_substr($message, $ivSize, null, '8bit');

        return openssl_decrypt($cipherText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $ivString);
    }

    public static function encryptArray($data, $salt, $excludedKeys = [])
    {
        if (!is_array($data) || count($data) == 0 || strlen($salt) < 1) {
            return null;
        }

        $encryptedArray = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $excludedKeys)) {
                $encryptedArray[$key] = $value;
            } else {
                $encryptedArray[$key] = self::encrypt($value, $salt);
            }
        }

        return $encryptedArray;
    }

    public static function decryptArray($data, $salt, $excludedKeys = [])
    {
        if (!is_array($data) || count($data) == 0 || strlen($salt) < 1) {
            return null;
        }

        $decryptedArray = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $excludedKeys)) {
                $decryptedArray[$key] = $value;
            } else {
                $decryptedArray[$key] = self::decrypt($value, $salt);
            }
        }

        return $decryptedArray;
    }

    public static function encryptDatasource(Datasource $datasource, $pepper = '', $excludedKeys = [])
    {
        foreach ($datasource->rows as $key => $row) {
            $datasource->rows[$key] = self::encryptArray($row, $row['salt'] . $pepper, $excludedKeys);
        }

        return $datasource;
    }

    public static function decryptDatasource(Datasource &$datasource, $pepper = '', $excludedKeys = [])
    {
        foreach ($datasource->rows as $key => $row) {
            $datasource->rows[$key] = self::decryptArray($row, $row['salt'] . $pepper, $excludedKeys);
        }

        return $datasource;
    }
}
