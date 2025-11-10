<?php
/**
 * Plugin Name: Kumar Jewelers Header & Footer
 * Plugin URI: https://kumarjewelers.com
 * Description: Custom Elementor widgets for Kumar Jewelers header and footer with full customization
 * Version: 1.0.0
 * Author: Kumar Jewelers
 * Author URI: https://kumarjewelers.com
 * Text Domain: kumar-jewelers
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Elementor tested up to: 3.18.0
 * Elementor Pro tested up to: 3.18.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main Kumar Jewelers Widgets Class
 */
final class Kumar_Jewelers_Widgets {
    
    /**
     * Plugin Version
     */
    const VERSION = '1.0.0';
    
    /**
     * Minimum Elementor Version
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    
    /**
     * Minimum PHP Version
     */
    const MINIMUM_PHP_VERSION = '7.4';
    
    /**
     * Instance
     */
    private static $_instance = null;
    
    /**
     * Instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }
        
        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }
        
        // Register widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        
        // Register widget styles
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_widget_styles']);
        
        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'enqueue_widget_scripts']);
        
        // Register custom category
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }
    
    /**
     * Admin notice for missing Elementor
     */
    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'kumar-jewelers'),
            '<strong>' . esc_html__('Kumar Jewelers Widgets', 'kumar-jewelers') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'kumar-jewelers') . '</strong>'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice for minimum Elementor version
     */
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'kumar-jewelers'),
            '<strong>' . esc_html__('Kumar Jewelers Widgets', 'kumar-jewelers') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'kumar-jewelers') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice for minimum PHP version
     */
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'kumar-jewelers'),
            '<strong>' . esc_html__('Kumar Jewelers Widgets', 'kumar-jewelers') . '</strong>',
            '<strong>' . esc_html__('PHP', 'kumar-jewelers') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Register Widgets
     */
    public function register_widgets($widgets_manager) {
        
        // Include widget files
        require_once(__DIR__ . '/widgets/kumar-header-widget.php');
        require_once(__DIR__ . '/widgets/kumar-footer-widget.php');
        
        // Register widgets
        $widgets_manager->register(new \Kumar_Header_Widget());
        $widgets_manager->register(new \Kumar_Footer_Widget());
    }
    
    /**
     * Enqueue widget styles
     */
    public function enqueue_widget_styles() {
        // Enqueue Google Fonts
        wp_enqueue_style(
            'kumar-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Jost:wght@300;400;500;600;700&display=swap',
            [],
            self::VERSION
        );
    }
    
    /**
     * Enqueue widget scripts
     */
    public function enqueue_widget_scripts() {
        // Reserved for future custom scripts if needed
    }
    
    /**
     * Add custom Elementor widget category
     */
    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'kumar-jewelers',
            [
                'title' => esc_html__('Kumar Jewelers', 'kumar-jewelers'),
                'icon' => 'fa fa-gem',
            ]
        );
    }
}

// Initialize the plugin
Kumar_Jewelers_Widgets::instance();