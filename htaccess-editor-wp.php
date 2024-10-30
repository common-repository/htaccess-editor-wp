<?php

/*
Plugin Name: Htaccess Editor WP
Plugin URI: https://github.com/s4gor/htaccess_editor_wp
Description: A simple plugin to edit htaccess file safely.
Version: 1.0
Author: Imran Hossain Sagor
Author URI: https://imransagor.codes
License: GPLv3
*/

/**
 *
 * @package htaccess_editor_wp
 *
 */

defined('ABSPATH') or die('Unauthorized Access');

if(!class_exists('Htaccess_editor_wp')) {
    class Htaccess_editor_wp {
        
        public function hewp_register() {
            add_action('admin_menu', array($this, 'hewp_add_admin_pages'));
            add_action( 'admin_enqueue_scripts', array( $this, 'hewp_enqueue' ) );
            add_filter('clean_url', [$this, 'hewp_script_async'], 11, 1);
            add_filter("plugin_row_meta", [$this, "hewp_meta"], 10, 2);
            add_filter( 'plugin_action_links', [$this, 'hewp_ads_action_links'], 10, 5 );
        }

        public function hewp_add_admin_pages() {
            add_submenu_page('tools.php', '.htaccess editor', '.htaccess editor WP', 'manage_options', 'htaccess_editor_wp', [$this, 'hewp_views']);
        }

        public function hewp_views() {
            require_once plugin_dir_path( __FILE__ ) . 'views/view.php';
        }


        public function activate() {
            flush_rewrite_rules();
        }

        public function deactivate() {
            flush_rewrite_rules();
        }

        public function hewp_enqueue() {
            wp_enqueue_style('htaccess-editor-wp', plugins_url( 'css/style.min.css', __FILE__ ));
            wp_enqueue_script('htaccess-editor-wp', plugin_dir_url(__FILE__) . 'js/scripts.min.js#async');
        }

        public function hewp_script_async($url) {
            if(strpos($url, '#async') === false) {
                return $url;
            } else {
                return str_replace('#async', '', $url) . "' async='async";
            }
        }

        public function hewp_footer_notice(){
            echo '<span id="footer-thankyou">Thank you for using <a href="#">.htaccess editor WP</a>. <a href="https://www.paypal.com/donate?hosted_button_id=LV33MVDQUBSYY" target="_blank">Buy Me a Coffee <span style="color: red">&#x2764;</span></a></span>';
        }

        public function hewp_thankyou() {
            add_filter("admin_footer_text", [$this, 'hewp_footer_notice']);
        }

        public function hewp_meta($links = [], $file = "") {
            if(strpos($file, "htaccess-editor-wp/htaccess-editor-wp.php") !== false) {
                $new_link = [
                    "donation" => '<a href="https://www.paypal.com/donate?hosted_button_id=LV33MVDQUBSYY" target="_blank">Buy Me a Coffee <span style="color: red">&#x2764;</span></a>'
                ];

                $links = array_merge($links, $new_link);
            }

            return $links;

        }

        public function hewp_ads_action_links( $links, $plugin_file ) {

            $plugin = plugin_basename( __FILE__ );

            if($plugin === $plugin_file) {
                $ads_links = [
                    '<a href="' . admin_url( 'tools.php?page=htaccess_editor_wp' ) . '">editor</a>',
                ];
                $links = array_merge($ads_links, $links);
            }
            return $links;
        }

    }

    if(class_exists( 'Htaccess_editor_wp' )) {

        $htaccess_editor_wp = new Htaccess_editor_wp();
        $htaccess_editor_wp->hewp_register();

    } else {
        die('Plugin internal code conflict');
    }

    register_activation_hook(__FILE__, [$htaccess_editor_wp, 'activate']);
    register_deactivation_hook(__FILE__, [$htaccess_editor_wp, 'deactivate']);

}
