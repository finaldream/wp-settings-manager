<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Core;

use ConfigManager\Templates\DefaultYamlTemplate;
use Symfony\Component\Yaml\Yaml;
use WP_CLI\Iterators\Exception;

class File
{
    protected $path;

    /**
     * Create new file
     * @param $path
     */
    function __construct($path)
    {
        $this->path = $path;
    }

    static function initFromTemplate($path)
    {
        try {
            $newFile = fopen($path, "w");
            fwrite($newFile, DefaultYamlTemplate::get());
            fclose($newFile);

            return new self($path);
        } catch (Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    /**
     * Check if file writable
     *
     * @return bool
     */
    function isWritable()
    {
        return is_writable($this->path);
    }

    function parse(){
        return Yaml::parse(file_get_contents($this->path));
    }
}