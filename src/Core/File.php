<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Core;

use ConfigManager\Templates\DefaultYamlTemplate;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class File
 * @package ConfigManager\Core
 */
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

    /**
     * @param $path
     * @return File
     */
    static function initFromTemplate($path)
    {
        try {
            $newFile = fopen($path, "w");

            if (!$newFile) {
                throw new \Exception('File open failed.');
            }

            fwrite($newFile, DefaultYamlTemplate::get());
            fclose($newFile);

            return new self($path);
        } catch (\Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    function exist()
    {
        return file_exists($this->path);
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

    /**
     * @return mixed
     */
    function parse() {
        try {
            return Yaml::parse(file_get_contents($this->path));
        } catch (ParseException $e) {
            \WP_CLI::log('Unable to parse the YAML string:' . $e->getMessage());
        }
    }
}