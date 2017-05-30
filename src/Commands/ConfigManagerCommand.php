<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Commands;

use ConfigManager\Core\File;

/**
 * Class ConfigManagerCommand
 * @package ConfigManager
 */
class ConfigManagerCommand extends BaseCommand
{
    /**
     * Create new configuration file from template
     *
     * ## EXAMPLES
     *
     * wp config init
     *
     * @param $args
     * @param $assoc_args
     */
    function init($args, $assoc_args) {
        $this->loadArgs($args);
        $path = $this->resolveFilePath();

        if(is_dir($path)) {
            $path = $path .'/'. $this->defaultFileName;
            $this->file = File::initFromTemplate($path);
        }
    }

    /**
     * Load all configuration from current file
     *
     * ## EXAMPLES
     *
     * wp config start
     *
     * @param $args
     * @param $assoc_args
     */
    function load($args, $assoc_args) {
    }
}