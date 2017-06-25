<?php
/**
 * WP-CLI Package bootstrap
 *
 * @author Louis Thai <louis.thai@finaldream.de>
 * @since 26.05.2017
 */

if ( ! class_exists( 'WP_CLI' ) ) {
    return;
}
$autoload = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    require_once $autoload;
}

WP_CLI::add_command('config', '\ConfigManager\Commands\ConfigManagerCommand');
