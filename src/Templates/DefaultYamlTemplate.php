<?php
/**
 * Default Yaml Template
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Templates;

/**
 * Class DefaultYamlTemplate
 * @package ConfigManager\Templates
 */
class DefaultYamlTemplate
{
    static $version = '1.0';

    /**
     * Return default Yaml template
     *
     * @return string
     */
    static function get()
    {
        $version = static::$version;

        return <<<YAML
# format version
version: $version

# configuration per blog
blogs:

  your-domain.com:

    # template name and template_root
    themes:
      template: twentyseventeen

    # represents only the enabled plugins per site. plugins not specified, will be disabled.
    plugins:
      contact-form-7: true
      
    # list of options as defined in the blog's wp_options table
    wp_options:
      sample_option: true

  dev.your-domain.com:
    
    # allows to use another blog-config as a base...
    extends: your-domain.com
    
    # override to disable any plugin
    plugins:
      contact-form-7: false
      
    wp_options:
      secondary_sample_option: 'bar'
YAML;
    }
}