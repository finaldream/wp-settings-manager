<?php
/**
 * Abstract command class
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Commands;

use ConfigManager\Core\File;
use ConfigManager\Core\Helper;

/**
 * Class BaseCommand
 * @package ConfigManager\Commands
 */
abstract class BaseCommand
{
    /**
     * @var float
     */
    protected static $version;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $defaultFileName = 'wp-config.yaml';

    /**
     * @var array
     */
    protected $flags = [
        'force' => false,
        'override-network-plugins' => true
    ];

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * @param array $args
     * @param array $assoc_args
     */
    protected function loadArgs($args, $assoc_args)
    {
        $this->flags = array_merge($this->flags, $assoc_args);
    }

    /**
     * Get the absolute path to the file.
     *
     * @param string $path
     *
     */
    protected function resolveFilePath($path = null)
    {
        if (is_null($path)) {
            $path = $this->getFlag('wp-config');
        }

        if (file_exists($path)) {
            $this->filePath = $path;
        } else {
            $dirname  = dirname($path);
            $filename = basename($path);
            $filename = (!empty($filename)) ? $filename : $this->defaultFileName;

            $relpath  = $dirname ? '/' . ltrim($dirname, '/'): '';
            $path     = getcwd() . $relpath .'/'. $filename;

            $this->filePath = realpath($path) ?: $path;
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function getFlag($key, $default = null)
    {
        return (!empty($this->flags[$key])) ? $this->flags[$key] : $default;
    }

    /**
     * Validate loaded config
     */
    protected function validateConfigs()
    {
        if (empty($this->configs)) {
            Helper::errorColorize('Could not load configuration from:', $this->filePath);
        }

        if(!$this->checkVersion()) {
            Helper::errorColorize('Invalid configuration version loaded from:', $this->filePath);
        }

        if(empty($this->configs['blogs'])) {
            Helper::errorColorize('Could not load blogs configuration from:', $this->filePath);
        }
    }

    /**
     * Load config from file
     */
    protected function loadConfigs()
    {
        $this->configs = $this->file->parse();
    }

    /**
     * Check config template version
     *
     * @return bool
     */
    protected function checkVersion()
    {
        return (!empty($this->configs['version'] && ($this->configs['version'] === static::$version)));
    }

    /**
     * Check if plugin match provided name
     *
     * @param string $key
     * @param string $plugin
     * @return bool
     */
    protected function isPluginMatch($key, $plugin)
    {
        return (strpos(plugin_basename($key), trim($plugin)) !== false);
    }
}