<?php

namespace Framework;

class Watchlist
{
    public static function add($element, $label = null, $returnStack = false, $clearStack = false)
    {
        static $stack;

        // main array holding the actual data.
        if ($returnStack) {
            return $stack;
        } else if ($clearStack) {
            $stack = null;
        } else {
            if (is_array($element) || is_object($element)) {
                $stack[] = '<pre>' . $label . ': ' . print_r($element, true) . '</pre>';
            } else {
                $stack[] = $label . ' : ' . $element;
            }
        }

        return true;
    }

    public static function printList($clearStack = true)
    {
        $stack        = self::add(null, null, true);
        $randomNumber = (rand(0, 100) * rand(0, 100));
        if (is_array($stack) && count($stack) > 0) {
            echo '<table border=1 align="center" cellpadding="5" id="Helpers_Watchlist_Table" class="Helpers_Watchlist_Table"  >';
            echo '<tr><td onclick="jQuery(\'.Helpers_Watchlist_Table_Rows_' . $randomNumber . '\').toggle()" style="cursor:pointer; ">[ + ] <b> Watchlist</b></td></tr>';
            for ($i = 0; $i < count($stack); $i++) {
                echo '<tr class="Helpers_Watchlist_Table_Rows_' . $randomNumber . '"><td>' . $stack[$i] . '</td></tr>';
            }

            echo '</table> <script>/* jQuery(\'.Helpers_Watchlist_Table_Rows_' . $randomNumber . '\').toggle() */</script>';
            // clear after printing
            if ($clearStack) {
                self::add(null, null, false, true);
            }
        }
    }

    public static function clear()
    {
        self::add(null, null, false, true);
    }

    public static function trace($levels = 10, $label = null)
    {
        $debugBacktrace  = debug_backtrace();
        $backtraceString = 'Backtrace: ';
        for ($i = 1; $i <= $levels; $i++) {
// $i>0, so that this method call would not be traced
            if ($debugBacktrace[$i]['file'] == '') {
                continue;
            }

            $args = (array) $debugBacktrace[$i]['args'];
            $backtraceString .= '<br /> ' . $debugBacktrace[$i]['file'] . ' [' . $debugBacktrace[$i]['line'] . ']: ' . $debugBacktrace[$i]['function'] . '(' . print_r(implode(', ', $args), true) . ') ';
        }

        self::add($backtraceString, $label);
    }

}
