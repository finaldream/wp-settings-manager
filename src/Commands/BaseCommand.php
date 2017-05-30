<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Commands;


abstract class BaseCommand
{
    protected $filePath;

    protected $file;

    protected $defaultFileName = 'wp-config.yaml';

    /**
     * @param $args
     */
    protected function loadArgs($args) {
        $this->filePath = $args[0];
    }

    /**
     * Get the absolute path to the file.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolveFilePath($path = null)
    {
        if (is_null($path)) {
            $path = $this->filePath;
        }

        if (file_exists($path)) {
            return $path;
        }

        $dirname  = dirname($path);
        $filename = basename($path);
        $relpath  = $dirname ? '/' . $dirname : '';
        $path     = getcwd() . $relpath .'/'. $filename;

        return realpath($path) ?: $path;
    }
}