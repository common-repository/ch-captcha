<?php
/**
 * Plugin Name: CH Captcha
 * Version: 1.0.1
 * Plugin URI: 
 * Description: You can add captcha in caontact 7 form
 * Author: Chetan Khandla
 * Author URI: 
 * Tested up to: 4.4.1
 *
 * Text Domain: wc-chc
 * Domain Path: /i18n/
 *
 * @author Nicola Mustone
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('error_reporting', 1);



final class WP_chc{
	
	protected static $_instance = null;

	public $plugin_path;
    public function __construct() {

        // Set up localization
        $this->load_plugin_textdomain();
        
		$this->plugin_path = $this->plugin_path();

		$this->includes();
		$this->hooks();
		do_action( 'ch_captcha_loaded' );
		
    }


	public function includes(){
		
		include_once($this->plugin_path."/includes/admin/chc-form-field.php");
		include_once($this->plugin_path."/includes/contact-from-captcha.php");
		include_once($this->plugin_path."/includes/function.php");
		
	}
	
	
	public function hooks(){
		add_action('init',array($this,'init'));
		
	}
	
	public function init(){
		
	}
	
	
	
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wc-ost' );

        load_textdomain( 'wc-ost', WP_LANG_DIR . '/woocommerce-order-search-transaction/wc-ost-' . $locale . '.mo' );
        load_textdomain( 'wc-ost', WP_LANG_DIR . '/plugins/wc-order-search-transaction-' . $locale . '.mo' );

        load_plugin_textdomain( 'wc-ost', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
    }
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	

	public function plugin_path($path) {
		return untrailingslashit( plugin_dir_path( __FILE__ ) )."/".$path;
	}
}

function WP_CHC() {
	return WP_chc::instance();
}

$GLOBALS['ch_captcha'] = WP_CHC();