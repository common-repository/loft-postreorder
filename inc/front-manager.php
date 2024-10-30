<?php
/**
* Front manager class
*/

if ( ! class_exists( 'Loft_Post_Reorder_Front_Manager' ) ) {
	class Loft_Post_Reorder_Front_Manager {
		/**
		* Make sure only one instance exists
		*/
		static public $_instance = false;
		/**
		* Construct function
		*/
		public function __construct() {
			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', array( $this, 'set_category_page_args' ), 99999 );
			}
		}/**
		* Set category page query args
		* @param object
		*/
		public function set_category_page_args( $query ) {
			if ( $query->is_main_query() && is_category() ) { 
				$cat_id = get_queried_object_id();
				$type = get_term_meta( $cat_id, 'loft_post_reorder_type', true ); 
				if ( 'manually' == $type ) {
					$query->set( 'orderby', array( 
						'meta_value_num' => 'ASC',
						'date' => 'DESC'
					) );
					$query->set( 'meta_key', LOFT_POST_REORDER_META_PREFIX . $cat_id );
				}
			}
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize front manager
	add_action( 'loftocean/post_reorder/load_modules', array( 'Loft_Post_Reorder_Front_Manager', '_instance' ) );
}