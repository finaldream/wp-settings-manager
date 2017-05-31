<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Core;

/**
 * Class Helper
 * @package ConfigManager\Core
 */
class Helper
{
    /**
     * @param $message
     * @param string $highlight
     */
    static function errorColorize($message, $highlight = '')
    {
        \WP_CLI::error(\WP_CLI::colorize($message . ' %Y' . $highlight . '%N'));
    }

    /**
     * @param $message
     * @param string $highlight
     */
    static function successColorize($message, $highlight = '')
    {
        \WP_CLI::success(\WP_CLI::colorize($message . ' %Y' . $highlight . '%N'));
    }
}