<?php
/*
Plugin Name: Pool Calculator
Description: A WordPress plugin to create a pool cost calculator with steps and selections.
Version: 1.0
Author: Christopher Dockens
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define constants
define('POOL_CALCULATOR_VERSION', '1.0');
define('POOL_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include admin and frontend functionality
require_once POOL_CALCULATOR_PLUGIN_DIR . 'includes/admin-settings.php';
require_once POOL_CALCULATOR_PLUGIN_DIR . 'includes/frontend-display.php';

// Enqueue styles and scripts
function pool_calculator_enqueue_assets() {
    wp_enqueue_style('pool-calculator-styles', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('pool-calculator-scripts', plugins_url('assets/js/pool-calculator.js', __FILE__), ['jquery'], null, true);
}

// Enqueue Google Fonts dynamically based on selected font
function pool_calculator_enqueue_google_fonts() {
    $selected_font = get_option('pool_calculator_selected_font', 'Roboto');
    $font_url = 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', $selected_font) . ':wght@400;600&display=swap';
    wp_enqueue_style('pool-calculator-google-font', $font_url, [], null);
}

add_action('wp_enqueue_scripts', 'pool_calculator_enqueue_assets');
add_action('wp_enqueue_scripts', 'pool_calculator_enqueue_google_fonts');
add_action('admin_enqueue_scripts', 'pool_calculator_enqueue_google_fonts');

// Inject selected font into inline CSS
function pool_calculator_add_inline_font_css() {
    $selected_font = get_option('pool_calculator_selected_font', 'Roboto');
    echo "<style>:root { --pool-calculator-font: '{$selected_font}', sans-serif; }</style>";
}
add_action('wp_head', 'pool_calculator_add_inline_font_css');
add_action('admin_head', 'pool_calculator_add_inline_font_css');
