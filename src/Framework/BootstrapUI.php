<?php

namespace Framework;

class BootstrapUI
{

    /**
     * Returns alert-danger
     *
     * @param $message
     * @return string
     */
    public static function alert($message)
    {
        return ' <div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle"></i> ' . $message . '  </p></div>';
    }

    /**
     * Returns alert-danger
     *
     * @param $message
     * @param bool $dismissible
     * @return string
     */
    public static function alertDanger($message, $dismissible = false)
    {
        if ($dismissible) {
            $returnString = '<div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Error: </strong> ' . $message . '</div>';
        } else {
            $returnString = '<div class="alert alert-danger" role="alert"><strong>Error: </strong> ' . $message . '</div>';
        }

        return $returnString;
    }

    /**
     * Returns alert-warning
     *
     * @param $message
     * @param bool $dismissible
     * @return string
     */
    public static function alertWarning($message, $dismissible = false)
    {
        if ($dismissible) {
            $returnString = '<div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Error: </strong> ' . $message . '</div>';
        } else {
            $returnString = '<div class="alert alert-warning" role="alert"><strong>Warning: </strong> ' . $message . '</div>';
        }

        return $returnString;
    }

    /**
     * Returns alert-success
     *
     * @param $message
     * @param bool $dismissible
     * @return string
     */
    public static function alertSuccess($message, $dismissible = false)
    {
        if ($dismissible) {
            $returnString = '<div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Message: </strong> ' . $message . '</div>';
        } else {
            $returnString = '<div class="alert alert-success" role="alert"><strong>Message: </strong> ' . $message . '</div>';
        }

        return $returnString;
    }

    /**
     * @param string $message
     * @param bool $showIcon
     * @param bool $dismissable
     * @return string
     */
    public static function alertInfo(string $message, bool $showIcon = false, bool $dismissable = false)
    {
        $icon = '';
        if ($showIcon) {
            $icon = '<i class="fa fa-info-circle"></i>';
        }

        if ($dismissable) {
            $returnString = '<div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ' . $icon . ' <strong>Message: </strong> ' . $message . '</div>';
        } else {
            $returnString = '<div class="alert alert-info" role="alert">' . $icon . ' <strong>Message: </strong> ' . $message . '</div>';
        }

        return $returnString;
    }

    /**
     * @param string $error
     * @param bool $showIcon
     * @param bool $dismissable
     * @return string
     */
    public static function error(string $error, bool $showIcon = false, bool $dismissable = false)
    {
        $icon = '';
        if ($showIcon) {
            $icon = '<i class="fa fa-exclamation-triangle"></i>';
        }

        if ($dismissable) {
            $returnString = '<div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ' . $icon . ' <strong>Error: </strong> ' . $error . '</div>';
        } else {
            $returnString = '<div class="alert alert-warning" role="alert">' . $icon . '<strong>Error: </strong> ' . $error . '</div>';
        }

        return $returnString;
    }
}
