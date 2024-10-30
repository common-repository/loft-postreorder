<?php
/*
Plugin Name: Loft PostReorder
Description: An easy to use plugin to reorder your posts on the Category Archive pages.
Version: 1.0.0
Author: Loft.Ocean
Author URI: http://www.loftocean.com/
Text Domain: loft-post-reorder
Domain Path: /assets/languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! class_exists( 'Loft_Post_Reorder' ) ) {
	class Loft_Post_Reorder {
		/**
		* Object class instance 
		*/
		private static $instance = null;
		/**
		* Meta name prefix
		*/
		public static $meta_name_prefix = 'loft_post_custom_order_for_category_';
		/**
		* Construct function
		*/
		public function __construct() { 
			$this->load_textdomain();
			$this->define_constants();
			$this->includes();
			$this->load_modules();
		}
		/**
		* Load text domain
		*/
		public function load_textdomain() {
			load_plugin_textdomain( 'loft-post-reorder' );
		}
		/*
		* Define constant needed
		*/
		private function define_constants() {
			$this->define( 'LOFT_POST_REORDER_VERSION', '1.0.0' );
			$this->define( 'LOFT_POST_REORDER_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'LOFT_POST_REORDER_URI', plugins_url( '/', __FILE__ ) );
			$this->define( 'LOFT_POST_REORDER_ASSETS_VERSION', '2019021201' );
			$this->define( 'LOFT_POST_REORDER_META_PREFIX', self::$meta_name_prefix );
		}
		/*
		* Helper function to define constants
		*/
		private function define( $name, $value ) {
			defined( $name ) ? '' : define( $name, $value );
		}
		/**
		* Include required files
		*/
		private function includes() {
			$inc = LOFT_POST_REORDER_DIR . 'inc/';

			require_once $inc . 'functions.php';
			require_once $inc . 'admin-manager.php';
			require_once $inc . 'front-manager.php';
		}
		/**
		* Load modules if they are enabled
		*/
		public function load_modules() {
			do_action( 'loftocean/post_reorder/load_modules' );
		}
		/**
		* @descirption initialize extenstion
		*/
		public static function _instance() { 
			if ( ! class_exists( 'Loft_Post_Reorder_Pro' ) ) {
				if ( null === self::$instance ) {
					self::$instance = new self();
				}
				return self::$instance;
			}
		}
	}
	add_action( 'init', 'Loft_Post_Reorder::_instance' );
}
