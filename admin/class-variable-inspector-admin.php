<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bowo.io
 * @since      1.0.0
 *
 * @package    Variable_Inspector
 * @subpackage Variable_Inspector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Variable_Inspector
 * @subpackage Variable_Inspector/admin
 * @author     Bowo <hello@bowo.io>
 */
class Variable_Inspector_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The database table name for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table = $wpdb->prefix . 'variable_inspector';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Variable_Inspector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Variable_Inspector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-fomantic-ui-tab', plugin_dir_url( __FILE__ ) . 'css/fomantic-ui/tab.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '-fomantic-ui-accordion', plugin_dir_url( __FILE__ ) . 'css/fomantic-ui/accordion.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/variable-inspector-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Variable_Inspector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Variable_Inspector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '-fomantic-ui-tab', plugin_dir_url( __FILE__ ) . 'js/fomantic-ui/tab.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . '-fomantic-ui-accordion', plugin_dir_url( __FILE__ ) . 'js/fomantic-ui/accordion.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/variable-inspector-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . '-jsticky-mod', plugin_dir_url( __FILE__ ) . 'js/jquery.jsticky.mod.min.js', array( 'jquery' ), $this->version, false );

		$vi_default_options = array(
			'viewer'	=> 'var_export',
		);
		$vi_options = get_option( 'variable_inspector', $vi_default_options );

		wp_localize_script(
			$this->plugin_name,
			'viVars',
			array(
				'viewer'	=> $vi_options['viewer'],
			)
		);

	}

	/**
	 * Add a variable to the inspector
	 *
	 * @since 1.0.0
	 */
	public function vi_inspect_variable( $args = array() ) {

		global $wpdb;

		$result = wp_cache_get( 'var_inspect_' . $args[0], 'variable-inspector' );

		if ( false === $result ) {

			$variable_type = gettype( $args[1] );

			$variable_name = $args[0];

			$variable_content = maybe_serialize( $args[1] );

			if ( !empty( $args[2] ) ) {

				$file_path = str_replace( ABSPATH, '', $args[2] );

			} else {

				$file_path = '';

			}

			// Line number in origin script

			if ( !empty( $args[3] ) ) {

				$line_number = absint( $args[3] );

			} else {

				$line_number = '';

			}

			// Add to databaase

			$result = $wpdb->insert( 
				$this->table, 
				array(
					'type'			=> $variable_type,
					'name'			=> $variable_name,
					'content'		=> $variable_content,
					'file_path'		=> $file_path,
					'line_number'	=> $line_number,
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
				)
			);

			// Cache the result for 1 seconds. This is to prevent duplicate entries in the DB table.
			wp_cache_set( 'var_inspect_' . $args[0], $result, 'variable-inspector', 1 );

		}

	}

	/**
	 * Clear database table
	 *
	 * @since 1.0.0
	 */
	public function vi_clear_results() {

		global $wpdb;

		$sql = "TRUNCATE {$this->table}";

		$wpdb->query( $sql );

		wp_die( json_encode( array( 'success' => true ) ) );

	}

	/**
	 * The variable inspector
	 *
	 * @since 1.0.0
	 */
	public function vi_inspection_results() {

		// Perform several test inspections

		// $count = 1024;
		// do_action( 'inspect', [ 'count', $count, __FILE__, __LINE__ ] );

		// $is_valid = true;
		// do_action( 'inspect', [ 'is_valid', $is_valid, __FILE__, __LINE__ ] );

		// $description = 'Lorem ipsum dolor siamet.';
		// do_action( 'inspect', [ 'description', $description, __FILE__, __LINE__ ] );

		// $vehicles = array( 'Car', 'Bicycle', 'Bus' );
		// do_action( 'inspect', [ 'vehicles', $vehicles, __FILE__, __LINE__ ] );

		// $vehicle_details = array(
		// 	'vehicle'		=> 'Bicycle',
		// 	'wheels'		=> 2,
		// 	'ecofriendly'	=> true,
		// );
		// do_action( 'inspect', [ 'vehicle_details', $vehicle_details, __FILE__, __LINE__ ] );

		// $vehicle_types = array(
		// 	'bicycle'	=> array(
		// 		'fuel'			=> 'food',
		// 		'wheels'		=> 2,
		// 		'ecofriendly'	=> true,
		// 	),
		// 	'car'	=> array(
		// 		'fuel'			=> 'gasoline',
		// 		'wheels'		=> 4,
		// 		'ecofriendly'	=> false,
		// 	),
		// );
		// do_action( 'inspect', [ 'vehicle_types', $vehicle_types, __FILE__, __LINE__ ] );

		// global $wp_roles;
		// do_action( 'inspect', [ 'wp_roles', $wp_roles, __FILE__, __LINE__ ] );

		// Get inspection results

		global $wpdb;

		$limit = 100;

		$sql = $wpdb->prepare( "SELECT * FROM {$this->table} ORDER BY ID DESC LIMIT %d", array( $limit ) );

		$inspection_results = $wpdb->get_results( $sql, ARRAY_A );

		// Output inspection results

		$output = '';

		$output .= '
			<div class="inspector-header">
				<h2>Results</h2>
				<div class="inspector-actions">
					<div class="results-operations">
						<select name="results_viewer" id="results_viewer" class="results-viewer" data-viewer="">
							<option value="var_export">var_export</option>
							<option value="var_dump">var_dump</option>
							<option value="print_r">print_r</option>
						</select>
						<a class="button toggle-results">Expand all</a>
					</div>
					<div class="results-options">
						<label for="auto_load" class="autorefresh-results"><input type="checkbox" id="auto_load" name="auto_load" value="auto_load">Auto refresh</label>
						<a class="button refresh-results" onclick="AjaxManual(\'#inspection-results\')">Refresh</a>
						<a class="button clear-results" href="" data-status="info">Clear</a>
					</div>
				</div>
			</div>';

		$output .= '<div id="inspection-results" class="inspection-results">';

		if ( empty( $inspection_results ) || ! is_array( $inspection_results ) ) {
			$output .= '<div class="no-results">There is no data in the inspection log.</div>';
		}

		foreach( $inspection_results as $variable ) {

			$inspection_time = date( 'H:i:s', strtotime( $variable['date'] ) );
			$inspection_time_hi = date( 'H:i', strtotime( $variable['date'] ) );
			$inspection_time_s = date( ':s', strtotime( $variable['date'] ) );
			$inspection_time_formatted = '<span class="time-hi">'. $inspection_time_hi . '</span><span class="time-s">'. $inspection_time_s . '</span>';
			$inspection_time_numeric = date( 'His', strtotime( $variable['date'] ) );

			$variable_type = $variable['type'];

			switch( $variable_type ) {

				case 'boolean':
				$variable_content = (bool) maybe_unserialize( $variable['content'] );
				break;

				case 'integer':
				$variable_content = (int) maybe_unserialize( $variable['content'] );
				break;

				case 'string':
				$variable_content = (string) maybe_unserialize( $variable['content'] );
				break;

				case 'array':
				$variable_content = (array) maybe_unserialize( $variable['content'] );
				break;

				case 'object':
				$variable_content = (object) maybe_unserialize( $variable['content'] );
				break;
			}

			$variable_name_plain = $variable['name'];
			$variable_name = '$' . $variable['name'];

			$identifier = $variable_name_plain .'-'. $inspection_time_numeric;

			$origin_script_path = $variable['file_path'];
			$origin_script_line = $variable['line_number'];

			$type_tag = '<span class="variable-type">' . esc_html( $variable_type ) . '</span>';

			ob_start();
			var_dump( $variable_content );
			$variable_content_vardump = ob_get_clean();

			$variable_content_varexport = var_export( $variable_content, true );

			if ( !empty( $origin_script_path ) ) {

				if ( !empty( $origin_script_line ) ) {

					$origin_script = $origin_script_path . ':' . $origin_script_line;

				} else {

					$origin_script = $origin_script_path;

				}

			}

			$output .= '<div class="inspection-result">';

			$output .= '<div class="inspection-time">' . $inspection_time_formatted . '</div>';

			$output .= '<div class="accordion inspection-accordion">';
			
			$output .= '<div class="accordion__control">' . esc_html( $variable_name ) . $type_tag . '<span class="accordion__indicator"></span></div>';

			$separator = '<span class="separator">&lrhar;</span>';

			$output .= '
						<div id="'. esc_html( $identifier ) .'" class="accordion__panel">
							<div class="functions">
								<a class="item" data-tab="third">var_export</a>'.$separator.'<a class="item" data-tab="second">var_dump</a>'.$separator.'<a class="item" data-tab="first">print_r</a>
							</div>
							<div class="ui tab" data-tab="third">
					       		<pre>' . $variable_content_varexport . '</pre>
							</div>
							<div class="ui tab" data-tab="second">
					       		<pre>' . $variable_content_vardump . '</pre>
							</div>
							<div class="ui tab" data-tab="first">
					       		<pre>' . print_r( $variable_content, true ) . '</pre>
							</div>
							<script>
								jQuery("#'. esc_html( $identifier ) .' .functions .item")
								  .tab({
								  		context: jQuery("#'. esc_html( $variable_name_plain .'-'. $inspection_time_numeric ) .'")
								  	})
								;
							</script>

						</div>
						';

			if ( !empty( $origin_script_path ) ) {

				$output .= '<div class="inspection-origin">' . esc_html( $origin_script ) .  '</div>';

			}

			$output .='</div>';

			$output .='</div>';

		}

		$output .='</div>';

		return $output;
	
	}

	/**
	 * Register admin menu
	 *
	 * @since 1.5.0
	 */
	public function vi_register_admin_menu() {

		add_submenu_page(
			'tools.php',
			__( 'Variable Inspector', 'variable-inspector' ),
			__( 'Variable Inspector', 'variable-inspector' ),
			'manage_options',
			'variable-inspector',
			[ $this, 'vi_create_main_page' ]
		);

	}

	/**
	 * Create the main page in wp-admin
	 *
	 * @since 1.5.0
	 */
	public function vi_create_main_page() {

		?>
		<div class="wrap vi">
			<div id="vi-header" class="vi-header">
				<div class="vi-header-left">
					<h1 class="vi-heading"><?php esc_html_e( 'Variable Inspector', 'variable-inspector' ); ?> <small><?php esc_html_e( 'by', 'variable-inspector' ); ?> <a href="https://bowo.io" target="_blank">bowo.io</a></small></h1>
				</div>
				<div class="vi-header-right">
					<a href="https://wordpress.org/plugins/variable-inspector/" target="_blank" class="vi-header-action"><span>&#8505;</span> <?php esc_html_e( 'Info', 'variable-inspector' ); ?></a>
					<a href="https://wordpress.org/plugins/variable-inspector/#reviews" target="_blank" class="vi-header-action"><span>★</span> <?php esc_html_e( 'Review', 'variable-inspector' ); ?></a>
					<a href="https://wordpress.org/support/plugin/variable-inspector/" target="_blank" class="vi-header-action">✚ <?php esc_html_e( 'Feedback', 'variable-inspector' ); ?></a>
					<a href="https://paypal.me/qriouslad" target="_blank" class="vi-header-action">&#10084; <?php esc_html_e( 'Donate', 'variable-inspector' ); ?></a>
				</div>
			</div>
			<div class="vi-body">
			<?php echo $this->vi_inspection_results(); ?>
			</div>
			<div class="vi-footer">
			  <div class="ui accordion">
			    <div class="title">
			      <i class="dropdown icon"></i>
			      How do I use Variable Inspector?
			    </div>
			    <div class="content">
			      <p>Simply place the following line anywhere in your code after the $variable_name you’d like to inspect:</p>
			      <pre><code>do_action( 'inspect', [ 'variable_name', $variable_name ] );</code></pre>
			      <p>If you’d like to record the originating PHP file and line number, append the PHP magic constants __FILE__ and __LINE__ as follows:</p>
			      <pre><code>do_action( 'inspect', [ 'variable_name', $variable_name, __FILE__, __LINE__ ] );</code></pre>
			      <p>This would help you locate and clean up the inspector lines once you’re done debugging.</p>
			    </div>
			  </div>
			</div>
		</div>
		<?php
	}

	/**
	 * Update footer text
	 *
	 * @since 1.5.0
	 */
	public function vi_footer_text() {
		?>
		<a href="https://wordpress.org/plugins/variable-inspector/" target="_blank">Variable Inspector</a> (<a href="https://github.com/qriouslad/variable-inspector" target="_blank">github</a>) is built with the <a href="https://github.com/devinvinson/WordPress-Plugin-Boilerplate/" target="_blank">WordPress Plugin Boilerplate</a>, <a href="https://wppb.me" target="_blank">wppb.me</a> and <a href="https://github.com/AndrewHenderson/jSticky" target="_blank">jSticky</a>.
		<?php
	}

	/**
	 * Add "Access Now" plugin action link
	 *
	 * @since 1.0.0
	 */
	public function vi_plugin_action_links( $links ) {

		$action_link = '<a href="tools.php?page=' . $this->plugin_name . '">Access Now</a>';

		array_unshift( $links, $action_link );

		return $links;

	}

	/**
	 * Set default / all results viewer, e.g. var_export
	 *
	 * @since 1.7.0
	 */
	public function vi_set_viewer() {

		if ( isset( $_REQUEST ) ) {

			$viewer = $_REQUEST['viewer'];

		    update_option( 
		    	'variable_inspector', 
		    	array( 
		    		'viewer' => $viewer, 
		    	), 
		    	false 
		    );

		    $response = array(
		    	'status'	=> 'success',
		    	'viewer'	=> $viewer,
		    );

		    echo json_encode( $response );

		}

	}

	/**
	 * To stop other plugins' admin notices overlaying in the Variable Inspector UI, remove them.
	 *
	 * @hooked admin_notices
	 *
	 * @since 1.7.1
	 */
	public function vi_suppress_admin_notices() {

		global $plugin_page;

		if ( 'variable-inspector' === $plugin_page ) {
			remove_all_actions( 'admin_notices' );
		}

	}

	/**
	 * To stop other plugins' admin notices overlaying in the Variable Inspector UI, remove them.
	 *
	 * @hooked admin_notices
	 *
	 * @since 1.7.2
	 */
	public function vi_suppress_all_admin_notices() {

		global $plugin_page;

		if ( 'variable-inspector' === $plugin_page ) {
			remove_all_actions( 'all_admin_notices' );
		}

	}

}
