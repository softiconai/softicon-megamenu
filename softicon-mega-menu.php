<?php
/*
*
* Plugin Name: SoftIcon Mega Menu
* Description: Add mega menus to the Navigation block in block themes. Design each dropdown panel in the Site Editor with any blocks.
* Version: 1.1.1
* Requires at least: 6.5
* Requires PHP: 7.1
* Author: softiconai
* Author URI: https://profiles.wordpress.org/softiconai/
* License: GPLv3 or later
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
* Text Domain: softicon-mega-menu
* Domain Path: /languages
*
*/


if ( !defined('ABSPATH') ) { exit; }

class ALMM_Mega_Menu{

    public static $instance;

    private function __construct()
    {
        $this->defined_constants();
        $this->load_classes();
    }

    public static function get_instance() {
        if( self::$instance ) {
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }

    public function defined_constants() {
        define('ALMM_PLUGIN_VERSION', '1.1.1');
        define('ALMM_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('ALMM_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    public function load_classes() {

        require_once ALMM_PLUGIN_PATH . 'includes/block.php';
        require_once ALMM_PLUGIN_PATH . 'includes/admin.php';

        new ALMM_Mega_Menu\block();
        new ALMM_Mega_Menu\admin();

        // add-ons such as SoftIcon Mega Menu Pro hook in here
        do_action( 'almm_loaded' );
    }
}
ALMM_Mega_Menu::get_instance();
