<?php
/**
 * Main WP-Config-Manager commands
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Commands;

use ConfigManager\Core\File;
use ConfigManager\Core\Helper;

/**
 * Class ConfigManagerCommand
 * @package ConfigManager\Commands
 */
class ConfigManagerCommand extends BaseCommand
{

    /**
     * @var float
     */
    protected static $version = 1.0;

    /**
     * @var array
     */
    protected static $supportedKeys = ['plugins', 'themes', 'wp_options'];

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
        }

        $this->file = File::initFromTemplate($this->filePath);

        if(!$this->file->isWritable() || !$this->file->exist()) {
            Helper::errorColorize('Can not create new configuration file at:', $this->filePath);
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
     * wp config load --wp-config=config/config.yaml --override-network-plugins=false
     *
     * @param $args
     * @param $assoc_args
     *
     * @synopsis [--wp-config=<file_path>] [--force] [--override-network-plugins=<boolean>]
     */
    function load($args, $assoc_args)
    {
        $this->loadArgs($args, $assoc_args);
        $this->resolveFilePath();
        $this->file = new File($this->filePath);

        if (!$this->file->exist()) {
            Helper::errorColorize('Could not found any configuration file at:', $this->filePath);
        }

        $this->loadConfigs();
        $this->validateConfigs();

        // Apply all configs
        $result = $this->applyConfigs();

        Helper::logColorize('Skipped', $result['skipped'] . ' blogs');
        Helper::successColorize('Loaded successfully', $result['completed'] . ' blogs');
    }

    /**
     * Apply all sites configuration
     */
    private function applyConfigs()
    {
        $blogMapping = $this->getBlogMapping();
        $skipped = 0;
        $completed = 0;

        foreach ($this->configs['blogs'] as $blogKey => $configs) {

            $blogId = $blogMapping[$blogKey];

            if (empty($blogId)) {
                $skipped++;
                continue;
            }

            switch_to_blog($blogId);

            Helper::logColorize('Switching to', $blogKey);
            Helper::logColorize('Applying configuration...');

            $extendedConfig = $this->getExtendedConfig($blogKey, $configs);

            $this->activatePlugins($extendedConfig['plugins']);
            $this->switchTheme($extendedConfig['themes']);
            $this->updateOptions($extendedConfig['wp_options']);

            $completed++;
        }

        restore_current_blog();

        return ['skipped' => $skipped, 'completed' => $completed];
    }

    /**
     * Activate multiple plugins by name
     *
     * @param $plugins
     */
    private function activatePlugins($plugins)
    {
        if(empty($plugins))
            return;

        // Deactivate all network plugins
        if ($this->getFlag('override-network-plugins')) {
            update_site_option('active_sitewide_plugins', []);
        }

        $availablePlugins = get_plugins();
        $matches = [];
        $errors = [];

        foreach ($plugins as $plugin => $enable) {

            // Skip when settings for plugin is FALSE
            if(!$enable)
                continue;

            foreach ($availablePlugins as $key => $data) {
                if (strpos(plugin_basename($key), trim($plugin)) !== false) {
                    $matches[] = $key;
                    continue 2;
                }
            }

            // match as error if not found
            $errors[] = $plugin;
        }

        if (!empty($errors)) {
            Helper::errorColorize('These plugins name are incorrect, please check again:', implode(', ', $errors));
        }

        // Deactivate current site plugins
        update_option('active_plugins', []);

        foreach ($matches as $match) {
            $result = activate_plugin($match);

            if ( is_wp_error( $result ) ) {
                \WP_CLI::log($result->get_error_message());
                Helper::errorColorize('Could not activate this plugin:', $match);
            }
        }

        Helper::successColorize('Activate all plugin successfully', count($matches) . ' plugins');
    }

    /**
     * Switch WP theme
     *
     * @param $theme
     */
    private function switchTheme($theme)
    {
        if(empty($theme))
            return;

        $availableThemes = wp_get_themes();
        $newTheme = $availableThemes[$theme['template']];

        if (empty($newTheme))
            Helper::errorColorize('Could not found this theme:', $theme['template']);

        if($newTheme instanceof \WP_Theme ) {
            switch_theme($newTheme->stylesheet);
            Helper::successColorize('Switch theme successfully', $theme['template']);
        }
    }

    /**
     * Update WP options
     *
     * @param $options
     */
    private function updateOptions($options)
    {
        if(empty($options))
            return;

        foreach ($options as $key => $value) {
            update_option($key, $value);
        }

        \WP_CLI::success('Update all options successfully');
    }

    /**
     * @param string $blogKey
     * @param array $configs
     *
     * @return array
     */
    private function getExtendedConfig($blogKey, $configs)
    {
        $base = $configs['extends'];
        $baseConfig = $this->configs['blogs'][$base];

        if (!empty($base) && !empty($baseConfig)) {
            foreach (static::$supportedKeys as $key) {
                $configs[$key] = (!empty($configs[$key])) ?
                    array_replace_recursive($baseConfig[$key], $configs[$key]) :
                    $baseConfig[$key];
            }
        }

        return $configs;
    }

    /**
     * Get network blog mapping
     *
     * @return array
     */
    private function getBlogMapping()
    {
        $blogs = get_sites();
        $_blogs = [];

        if(empty($blogs))
            \WP_CLI::error('No blogs found');

        foreach ($blogs as $blog) {
            $_blogs[$blog->domain] = $blog->blog_id;
        }

        return $_blogs;
    }
}