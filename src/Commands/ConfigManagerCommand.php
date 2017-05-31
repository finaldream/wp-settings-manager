<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Commands;

use ConfigManager\Core\File;
use ConfigManager\Core\Helper;

/**
 * Class ConfigManagerCommand
 * @package ConfigManager
 */
class ConfigManagerCommand extends BaseCommand
{

    /**
     * Create new configuration file from template
     *
     * ## OPTIONS
     *
     * [--wp-config]
     * : Relative path to store yaml configuration file
     *
     * [--force]
     * : Force to override existing file
     *
     * ## EXAMPLES
     *
     * wp config init --wp-config=config/config.yaml --force
     *
     * @param $args
     * @param $assoc_args
     *
     * @synopsis [--wp-config=<file_path>] [--force]
     */
    function init($args, $assoc_args)
    {
        $this->loadArgs($args, $assoc_args);
        $this->resolveFilePath();

        if (file_exists($this->filePath) && !$this->getFlag('force')) {
            Helper::errorColorize('Configuration file already exists at:', $this->filePath);
            return;
        }

        $this->file = File::initFromTemplate($this->filePath);

        if(!$this->file->isWritable() || !$this->file->exist()) {
            Helper::errorColorize('Can not create new configuration file at:', $this->filePath);
            return;
        }

        Helper::successColorize('Configuration file created at:', $this->filePath);
    }

    /**
     * Load all configuration from current file
     *
     * ## OPTIONS
     *
     * [--wp-config]
     * : Relative path to store yaml configuration file
     *
     * ## EXAMPLES
     *
     * wp config load --wp-config=config/config.yaml
     *
     * @param $args
     * @param $assoc_args
     */
    function load($args, $assoc_args)
    {
        $this->loadArgs($args, $assoc_args);
        $this->resolveFilePath();
        $this->file = new File($this->filePath);

        if (!$this->file->exist()) {
            Helper::errorColorize('Could not found any configuration file at:', $this->filePath);
            return;
        }

        $this->loadConfigs();

        Helper::successColorize('Config loaded from:', $this->filePath);
    }
}