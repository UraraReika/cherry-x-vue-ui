<?php
/**
 * Vue.js based Interface Builder module
 *
 * Version: 0.0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Vue_UI' ) ) {

	/**
	 * Class Cherry Interface Builder.
	 *
	 * @since 1.0.0
	 */
	class CX_Vue_UI {

		/**
		 * Module directory path.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var srting.
		 */
		protected $path;

		/**
		 * Module directory URL.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var srting.
		 */
		protected $url;

		/**
		 * Module version
		 *
		 * @var string
		 */
		protected $version = '1.0.0';

		/**
		 * CX_Vue_UI constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( array $args = array() ) {

			$this->path = ! empty( $args['path'] ) ? $args['path'] : false;
			$this->url  = ! empty( $args['url'] ) ? $args['url'] : false;

			if ( ! $this->path || ! $this->url ) {
				wp_die(
					'CX_Vue_UI not initialized. Module URL and Path should be passed into constructor',
					'CX_Vue_UI Error'
				);
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		}

		/**
		 * Enqueue builder assets
		 *
		 * @return void
		 */
		public function enqueue_assets() {

			$suffix = '.min';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$suffix = '';
			}

			wp_register_script(
				'cx-vue',
				$this->url . 'assets/js/vue' . $suffix . '.js',
				array(),
				'2.6.10',
				true
			);

			wp_enqueue_script(
				'cx-vue-ui',
				$this->url . 'assets/js/cx-vue-ui' . $suffix . '.js',
				array( 'cx-vue' ),
				$this->version,
				true
			);

			add_action( 'admin_footer', array( $this, 'print_templates' ), 0 );

			wp_enqueue_style(
				'cx-vue-ui',
				$this->url . 'assets/css/cx-vue-ui.css',
				array(),
				$this->version
			);

		}

		/**
		 * Returns registered components list
		 *
		 * @return array
		 */
		public function components_list() {
			return apply_filters( 'cx-vue-ui/components-list', array(

				// Layout elements
				'title',
				'collapse',
				'component-wrapper',
				'button',
				'repeater',
				'repeater-item',

				// Form elements
				'input',
				'switcher',
				'iconpicker',
				'select',
				'f-select',

			) );
		}

		/**
		 * Print components templates
		 *
		 * @return void
		 */
		public function print_templates() {

			$path        = $this->path . 'components/*.php';
			$whitelisted = $this->components_list();

			foreach ( glob( $path ) as $file ) {

				$slug = basename( $file, '.php' );

				if ( ! in_array( $slug, $whitelisted ) ) {
					continue;
				}

				ob_start();
				include $file;
				$template = ob_get_clean();

				printf(
					'<script type="text/x-template" id="%2$s">%1$s</script>',
					$template,
					'cx-vui-' . $slug
				);

			}

		}

	}

}
