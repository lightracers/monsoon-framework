<?php

namespace Framework;

/*
 * class to get input data from _REQUEST and _POST
 */

class Request
{
    public static function input($paramName, $escapeHtml = true, $allowBasicFormatTags = false)
    {
        $inputData    = json_decode(file_get_contents('php://input'), true);
        $filteredData = [];
        if (is_array($inputData)) {
            foreach ($inputData as $key => $value) {
                if (is_array($paramName) && in_array($key, $paramName)) {
                    $filteredData[$key] = Security::escapeString($inputData[$key], $escapeHtml, $allowBasicFormatTags);
                }

                if ($key == $paramName) {
                    $filteredData = Security::escapeString($inputData[$key], $escapeHtml, $allowBasicFormatTags);
                }
            }
        }

        return $filteredData;
    }

    public static function get($paramName, $escapeHtml = true, $allowBasicFormatTags = false)
    {
        return Security::escapeString($_GET[$paramName], $escapeHtml, $allowBasicFormatTags);
    }

    public static function post($paramName, $escapeHtml = true, $allowBasicFormatTags = false, $aliasFields = false)
    {
        if (is_array($paramName)) {
            $postArray = [];
            for ($i = 0; $i < count($paramName); $i++) {
                $key = !empty($aliasFields[$i]) ? $aliasFields[$i] : $paramName[$i];
                if (!isset($_POST[$paramName[$i]])) {
                    continue;
                }

                if ($escapeHtml) {
                    $postArray[$key] = Security::escapeString($_POST[$paramName[$i]], $escapeHtml, $allowBasicFormatTags);
                } else {
                    $postArray[$key] = $_POST[$paramName[$i]];
                }
            }

            return $postArray;
        } else {
            if (isset($_POST[$paramName]) && is_array($_POST[$paramName])) {
                $postArray = [];
                foreach ($_POST[$paramName] as $key => $value) {
                    if ($escapeHtml) {
                        $postArray[$key] = Security::escapeString($value, $escapeHtml, $allowBasicFormatTags);
                    } else {
                        $postArray[$key] = $_POST[$paramName][$key];
                    }
                }

                return $postArray;
            } else {
                return Security::escapeString(($_POST[$paramName] ?? null), $escapeHtml, $allowBasicFormatTags);
            }
        }
    }

    public static function files($fieldNames)
    {
        //from App/Config/Config.php
        $maxFileSizeAllowed = Box::getApplication('uploadMaxFilesize');

        //validations
        if (empty($_FILES[$fieldNames])) {
            return false;
        } else if ($_FILES[$fieldNames]['name'] == '' || $_FILES[$fieldNames]['tmp_name'] == '') {
            return ['error' => 'Invalid upload'];
        } else if (!is_uploaded_file($_FILES[$fieldNames]['tmp_name'])) {
            return false;
        } else if ($_FILES[$fieldNames]['error'] != '') {
            return ['error' => $_FILES[$fieldNames]['error']];
        } else if ($_FILES[$fieldNames]['size'] > $maxFileSizeAllowed) {
            return ['error' => 'File size exceeds maximum allowed size of ' . ($maxFileSizeAllowed / 1024 / 1024) . 'MB'];
        } else if (Utilities::strposArray($_FILES[$fieldNames]['name'], ["/", "\\", '..', '"', "'"]) !== FALSE) {
            return ['error' => 'File name contains invalid characters'];
        } else {
            // validations ok, so return data
            return $_FILES[$fieldNames];
        }
    }

    public static function cookie($paramName, $escapeHtml = true, $allowBasicFormatTags = false)
    {
        return Security::escapeString($_COOKIE[$paramName], $escapeHtml, $allowBasicFormatTags);
    }

    /**
     * detect if request is Ajax
     *
     * @static static method
     * @return boolean
     */
    public static function isXhr()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }

        return false;
    }

    /**
     * detect if request is POST request
     *
     * @static static method
     * @return boolean
     */
    public static function isPost()
    {
        return isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'POST';
    }

    /**
     * detect if request is GET request
     *
     * @static static method
     * @return boolean
     */
    public static function isGet()
    {
        return isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'GET';
    }

}
