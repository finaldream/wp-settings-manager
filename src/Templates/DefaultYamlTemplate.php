<?php
/**
 * TODO: Add description here
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 29.05.2017
 */

namespace ConfigManager\Templates;

class DefaultYamlTemplate
{
    static $version = '1.0';

    static function get()
    {
        $version = static::$version;

        return <<<YAML
# format version
version: $version

# configuration per blog
blogs:

  your-domain.com: 

    # list of options as defined in the blog's wp_options table
    wp_options: 
      template: twentyseventeen
      template_root: /wp-content/themes/twentyseventeen

    # represents only the enabled plugins per site. plugins not specified, will be disabled.
    plugins: 
      - contact-form-7

  dev.your-domain.com:
    extends: your-domain.com
    wp_options: 
      template_root: /var/www/html
YAML;
    }
}