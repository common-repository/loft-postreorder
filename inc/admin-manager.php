<?php
/**
* Add post reorder settings for Category
*/

if ( ! class_exists( 'LoftPost_Reorder_Admin_Manager' ) ) {
	class Loft_Post_Reorder_Admin_Manager {
		/**
		* Object current class instance to make sure only one instance exists
		*/
		static public $_instance = false;
		/**
		* Array to tell the taxonomy supported
		*/
		public $tax = array();
		/**
		* String category post order types
		*/
		protected $types = array();
		/**
		* Construct function
		*/
		public function __construct() {
			$this->tax = loft_post_reorder_taxonomy_supported();
			if ( is_array( $this->tax ) ) {
				$this->tax = array_filter( $this->tax );
				if ( ( is_admin() || wp_doing_ajax() ) && ! empty( $this->tax ) ) {
					$this->types = array( 
						'default' => esc_html__( 'Default', 'loft-post-reorder' ), 
						'manually' => esc_html__( 'Manual Order', 'loft-post-reorder') 
					);
					add_action( 'save_post', array( $this, 'save_meta' ), 10, 3 );
					add_action( 'edited_term', array( $this, 'save_tax_fileds' ), 10, 3 );
					add_action( 'admin_print_scripts-edit-tags.php', array( $this, 'enqueue_scripts' ) );
					add_action( 'admin_print_scripts-term.php', array( $this, 'enqueue_scripts' ) );
					foreach ( $this->tax as $tax ) {
						add_action( $tax . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ) );
					}
				}
			}
		}
		/**
		* Add a post order settings
		* @param object term
		*/
		public function edit_taxonomy_fields( $tag ) {
			$tax = $this->tax;
			$taxonomy = $tag->taxonomy;
			if ( in_array( $taxonomy, $tax ) ) {
				$tax_id = $tag->term_id;
				$args = array(
					'cat' => $tax_id,
					'posts_per_page' => -1,
				); 
				if ( 'yes' == get_term_meta( $tax_id, 'loft_post_reorder_custom_order_done', true ) ) {
					$args['meta_key'] = LOFT_POST_REORDER_META_PREFIX . $tax_id;
					$args['orderby'] = array( 
						'meta_value_num' => 'ASC',
						'date' => 'DESC'
					);
				}
				$posts = new WP_Query( $args );
				if ( $posts->have_posts() ) {
					$classes = array( 'form-field', 'loft-post-reorder-list-wrapper' );
					$type = get_term_meta( $tax_id, 'loft_post_reorder_type', true ); 
					if ( 'manually' != $type ) { 
						array_push( $classes, 'hide' ); 
					}; ?>

					<tr class="form-field">
						<th><?php esc_html_e( 'Posts Order: ' ); ?></th>
						<td>
							<select name="loft_post_reorder_type"> 
							<?php foreach( $this->types as $id => $label ) : ?>
								<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $type ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
						<th></th>
						<td>
							<ul class="loft-post-reorder-list">
							<?php while( $posts->have_posts() ) : ?>
								<?php $posts->the_post(); ?>
								<li>
									<input type="hidden" name="loft_post_order[]" value="<?php the_ID(); ?>" />
									<span class="item-post-title"><?php the_title(); ?></span>
								</li>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
							</ul>
							<button class="button button-primary loft-post-reset-custom-order">
								<?php esc_html_e( 'Reset the Custom Order', 'loft-post-reorder' ); ?>
							</button>
						</td>
					</tr><?php
				}
			}
		}
		/*
		* Save taxonomy settings
		*/
		public function save_tax_fileds( $term_id, $tt_id, $taxonomy ) {
			$tax = $this->tax;
			if ( in_array( $taxonomy, $tax ) && isset( $_REQUEST['loft_post_reorder_type'] ) ) { 
				if ( isset( $_REQUEST['loft-post-reset-custom-orders'] ) && ( 'reset' == $_REQUEST['loft-post-reset-custom-orders'] ) ) {
					$orders = empty( $_REQUEST['loft_post_order'] ) ? array() : $_REQUEST['loft_post_order'];
					foreach ( $orders as $index => $pid ) {
						update_post_meta( $pid, LOFT_POST_REORDER_META_PREFIX . $term_id, 1 );
					}
				} else {
					$orders = empty( $_REQUEST['loft_post_order'] ) ? array() : $_REQUEST['loft_post_order'];
					$type = $_REQUEST['loft_post_reorder_type'];
					update_term_meta( $term_id, 'loft_post_reorder_type', $type );
					if ( 'manually' == $type ) {
						update_term_meta( $term_id, 'loft_post_reorder_custom_order_done', 'yes' );
						foreach ( $orders as $index => $pid ) {
							update_post_meta( $pid, LOFT_POST_REORDER_META_PREFIX . $term_id, ( $index + 100 ) );
						}
					}
				}
			}
		}
		/**
		* Save post metas
		* @param int post id
		* @param object
		* @param int
		*/
		public function save_meta( $post_id, $post, $update ) {
			$post_types = array( 'post' );
			if ( empty( $update ) || ! in_array( $post->post_type, $post_types ) ) {
				return '';
			} 
			if ( current_user_can( 'edit_post', $post_id ) ) { 
				loft_post_reorder_update_post_order( $post_id, LOFT_POST_REORDER_META_PREFIX );
			}
		}
		/*
		* Enqueue scripts needed
		*/
		public function enqueue_scripts() {
			$uri = LOFT_POST_REORDER_URI . 'assets/';
			$suffix = LOFT_POST_REORDER_ASSETS_VERSION;
			wp_enqueue_style( 'loft-post-reorder', $uri . 'css/admin-style.min.css', array(), $suffix );
			wp_enqueue_script( 'loft-post-reorder-script', $uri . 'js/admin-script.min.js', array( 'jquery', 'jquery-ui-sortable' ), $suffix, true );
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
	// Add action to initialize admin manager
	add_action( 'loftocean/post_reorder/load_modules', array( 'Loft_Post_Reorder_Admin_Manager', '_instance' ) );
}