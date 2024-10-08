<?php
/**
 * Plugin Name: WP Leadgen Pro
 * Description: Plugin to integration with wp leadgen pro platform.
 * Version: 1.0.12
 * Author: WP Leadgen Pro
 * Author URI: https://wpleadgen.pro
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: leadgenpro
 */

if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists('LGP_Updater')) {
    include_once (plugin_dir_path(__FILE__) . 'updater.php');
}

$updater = new LGP_Updater(__FILE__);
$updater->set_username('jagdish1o1');
$updater->set_repository('wp-leadgen-pro-plugin');
$updater->initialize();

include_once plugin_dir_path(__FILE__) . 'includes/class-listings-cpt.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-services-cpt.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-states-tax.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-lgp-shortcodes.php';
include_once plugin_dir_path(__FILE__) . 'includes/modifier/rankmath.php';
include_once plugin_dir_path(__FILE__) . 'activate.php';
include_once plugin_dir_path(__FILE__) . 'deactivate.php';


register_activation_hook(__FILE__, 'leadgenpro_plugin_activate');
register_deactivation_hook(__FILE__, 'leadgenpro_plugin_deactivate');


function leadgenpro_init()
{
    if (!class_exists('ACF')) {

        $acfVersion = 'v6.3.5';
        include_once plugin_dir_path(__FILE__) . 'includes/acf-' . $acfVersion . '/acf.php';

        $statesTax = new LGP_StateTax();
        $statesTax->register();

        $listings = new LGP_ListingsCPT();
        $listings->register();

        $services = new LGP_ServicesCPT();
        $services->register();

        $settings = new LGP_SettingsPage();
        $settings->register();

        $shortcodes = new LGP_Shortcodes();
        $shortcodes->register();

        $modifier = new LGP_RankMath_Modifier();
        $modifier->register();

        add_filter('acf/settings/show_admin', '__return_false');

    }
}

add_action('plugins_loaded', 'leadgenpro_init');






