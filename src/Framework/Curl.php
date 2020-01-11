<?php

/**
 * Curl class
 */

namespace Framework;

/**
 * Sets some default functions and settings.
 */
class Curl
{
    /**
     * Performs a GET request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array $fields
     * @param array $options
     * @return bool|string
     *
     * @return string returns the content of the given url
     */
    public static function get($url, $params = [], $options = [])
    {
        $options[CURLOPT_URL] = $url . '?' . http_build_query($params, '', '&');

        return self::makeRequest($url, $params, $options);
    }

    /**
     * Performs a POST request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array $fields
     * @param array $options
     * @return bool|string
     *
     * @return string returns the content of the given url after post
     */
    public static function post($url, $fields = [], $options = [])
    {
        $options[CURLOPT_POSTFIELDS] = $fields;
        $options[CURLOPT_POST]       = true;

        return self::makeRequest($url, $fields, $options);
    }

    /**
     * Performs a PUT request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array $fields
     * @param array $options
     * @return bool|string
     *
     * @return string with the contents of the site
     */
    public static function put($url, $fields = [], $options = [])
    {
        $options[CURLOPT_POSTFIELDS]    = is_array($fields) ? http_build_query($fields) : $fields;
        $options[CURLOPT_CUSTOMREQUEST] = "PUT";

        return self::makeRequest($url, $fields, $options);
    }

    /**
     * Performs a DELETE request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array $fields
     * @param array $options
     * @return bool|string
     */
    public static function delete($url, $fields = [], $options = [])
    {
        $options[CURLOPT_POSTFIELDS]    = is_array($fields) ? http_build_query($fields) : $fields;
        $options[CURLOPT_CUSTOMREQUEST] = "DELETE";

        return self::makeRequest($url, $fields, $options);
    }

    /**
     * Performs the actual request
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return bool|string
     */
    protected static function makeRequest($url, $params, $options)
    {
        $c = \curl_init($url);

        // disable ssl verification (can be overwritten via options)
        \curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        \curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

        // set defaults
        $options[CURLOPT_URL]            = ($options[CURLOPT_URL] ?? $url);
        $options[CURLOPT_CONNECTTIMEOUT] = ($options[CURLOPT_CONNECTTIMEOUT] ?? 10);
        $options[CURLOPT_RETURNTRANSFER] = ($options[CURLOPT_RETURNTRANSFER] ?? true);
        $options[CURLOPT_USERAGENT]      = ($options[CURLOPT_USERAGENT] ?? 'curlUserAgent');

        if (!empty($options)) {
            \curl_setopt_array($c, $options);
        }

        $response = \curl_exec($c);
        \curl_close($c);

        return $response;

    }

    /**
     * @param string $url
     * @return bool|string
     */
    public static function ping($url)
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_NOBODY, 1);
        \curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return \curl_exec($ch);
    }

}
