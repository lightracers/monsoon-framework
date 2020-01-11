<?php

namespace Framework;

class Utilities
{

    /**
     * Opposite to nl2br()
     *
     * @param string $text
     * @return mixed
     */
    public static function br2nl($text)
    {
        return str_replace(["<br />", "<br>"], "", $text);
    }

    /**
     * Add slashes in Array
     *
     * @param $arr
     */
    public static function addSlashesInArray(&$arr)
    {
        if (is_array($arr)) {
            // if array is passed
            foreach ($arr as $col => $value) {
                $arr[$col] = addslashes($value);
            }
        }
    }

    /**
     * Replace html tags and others from entities
     *
     * @param $arr
     */
    public static function stripSlashesInArray(&$arr)
    {
        if (is_array($arr)) {
            // if array is passed
            foreach ($arr as $col => $value) {
                $arr[$col] = stripslashes($value);
            }
        }
    }

    public static function replaceString($text, $replaceHtml = false, $preserveBreakTags = false)
    {
        if (!is_string($text)) {
            return '';
        }

        if ($replaceHtml && $preserveBreakTags) {
            return stripslashes(html_entity_decode($text, ENT_QUOTES));
        } else if ($replaceHtml && !$preserveBreakTags) {
            return stripslashes(self::br2nl(html_entity_decode($text, ENT_QUOTES)));
        } else {
            return stripslashes(self::br2nl($text));
        }
    }


    /**
     * Outputs javascript to restore form values during postbacks
     *
     * @param $formName
     */
    public static function restoreForm($formName)
    {
        $valsToDisplay = null;
        echo '<script language="javascript" type="text/javascript">' . "\n";

        /*
         * If specific fields are asked
         */
        if (func_num_args() > 1) {
            // if specific fields are asked.
            for ($i = 1; $i < func_num_args(); $i++) {
                $valsToDisplay[func_get_arg($i)] = $_POST[func_get_arg($i)];
            }
        } else {
            // display all
            $valsToDisplay = $_POST;
        }

        foreach ($valsToDisplay as $field => $value) {
            // $value = $this::escapeString($value, true);
            // remove xss

            $value = strip_tags($value);
            $value = str_replace("\'", '"', $value);
            $value = str_replace('"', '\"', $value);
            $value = str_replace("\r\n", "\n", $value);
            $value = str_replace("\n", '\\n"+' . "\n" . '"', $value);

            $value = str_replace(
                [
                    '";alert(',
                    'alert(',
                    ')//',
                    '"//',
                    '//',
                ],
                '',
                $value
            );

            // */
            if ($field != 'PHPSESSID' and $value != '' and !is_int($field)) {
                echo 'document.' . $formName . '.' . $field . '.value="' . self::escapeString($value) . '"' . ";\n";
            }
        }

        echo '</script>';
    }

    /**
     * Restore form values from the given array
     *
     * @param $formName
     * @param $dataArray
     */
    public static function restoreFormFromArray($formName, $dataArray)
    {
        if (!is_array($dataArray) || count($dataArray) == 0) {
            return;
        }

        echo '<script language="javascript" type="text/javascript">' . "\n";

        foreach ($dataArray as $field => $value) {
            $value = self::replaceString($value, true, true);

            $value = str_replace("\'", '"', $value);
            $value = str_replace('"', '\"', $value);
            $value = str_replace("\r\n", "\n", $value);
            $value = str_replace("\n", '\\n"+' . "\n" . '"', $value);

            echo 'document.' . $formName . '.' . $field . '.value="' . $value . '"' . ";\n";
        }

        echo '</script>';
    }

    /**
     * Convert bytes into friendly memory sized
     *
     * @param $sizeInBytes
     * @return string
     */
    public static function formatMemorySize($sizeInBytes)
    {
        if ($sizeInBytes < 1024) {
            return (int) $sizeInBytes . " B";
        } else if ($sizeInBytes < (1024 * 1024)) {
            $sizeInBytes = round(($sizeInBytes / 1024), 1);
            return $sizeInBytes . " KB";
        } else if ($sizeInBytes < (1024 * 1024 * 1024)) {
            $sizeInBytes = round(($sizeInBytes / (1024 * 1024)), 1);
            return $sizeInBytes . " MB";
        } else {
            return round(($sizeInBytes / (1024 * 1024 * 1024)), 1) . " GB";
        }
    }

    /**
     * Generate HTML view of email
     *
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @return string
     */
    public static function emailPreview($from, $to, $subject, $message)
    {
        return '<table width="100%" border="1">
<tr><td colspan="2">e-Mail Preview</td></tr>
<tr><td>From: </td><td>' . $from . '</td></tr>
<tr><td>To: </td><td>' . $to . '</td></tr>
<tr><td>Subject: </td><td>' . $subject . '</td></tr>
<tr><td valign="top">Message: </td><td>' . $message . '</td></tr>
</table>';
    }

    /**
     * Generate random string of given length
     *
     * @param $length
     * @return string
     */
    public static function generateRandomString($length)
    {
        return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length));
    }

    /**
     * Find position of string gin the array
     *
     * @param $haystack
     * @param $needles
     * @return bool|int
     */
    public static function strposArray($haystack, $needles)
    {
        if (is_array($needles)) {
            foreach ($needles as $str) {
                if (is_array($str)) {
                    $pos = strpos_array($haystack, $str);
                } else {
                    $pos = strpos($haystack, $str);
                }

                if ($pos !== FALSE) {
                    return $pos;
                }
            }

            return FALSE;
        } else {
            return strpos($haystack, $needles);
        }
    }

    public static function convertCamelCaseToSnake($input)
    {
        if (preg_match('/[A-Z]/', $input) === 0) {
            return $input;
        }

        $output = strtolower(
            preg_replace_callback(
                '/([a-z])([A-Z])/',
                function ($string) {
                    return $string[1] . "_" . strtolower($string[2]);
                },
                $input
            )
        );
        return $output;
    }
}
