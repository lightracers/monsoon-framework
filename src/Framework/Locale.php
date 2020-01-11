<?php

/**
 * Locale - simple language handler.
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 2.2
 * @date November 18, 2014
 * @date updated Sept 19, 2015
 */

namespace Framework;

/**
 * Locale class to load the requested language file.
 */
class Locale
{

    /**
     * Variable holds array with language.
     *
     * @var array
     */
    public static $array;

    /**
     * @var string
     */
    private $language;

    /**
     * Load language function.
     *
     * @param string $name
     * @param string $code
     * @return mixed
     */
    public function load($name, $code = null)
    {
        if ($code == null) {
            return null;
        }

        /*
         * lang file
         */
        $file = ROOT . "/data/locale/$code/$name.php";
        /*
         * check if is readable
         */
        if (is_readable($file)) {
            /*
             * require file
             */
            self::$array = include($file);
        } else {
            /*
             * display error
             */
            echo Error::display("Could not load language file '$code/$name.php'");
            exit;
        }
    }

    /**
     * Get element from language array by key.
     *
     * @param string $value
     *
     * @return string
     */
    public function get($value)
    {
        if (!empty(self::$array[$value])) {
            return self::$array[$value];
        } else {
            return $value;
        }
    }

    /**
     * Get lang for views.
     *
     * @param string $value
     *            this is "word" value from language file
     *
     * @return string
     */
    public static function show($value)
    {
        if (!empty(self::$array[$value])) {
            return self::$array[$value];
        } else {
            return $value;
        }
    }

}
