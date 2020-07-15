<?php
/**
 *
 * @wordpress-plugin: Uli Contact Bar
 */

class Uli_Contact_Bar {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0
	 *
	 * @var     string
	 */
	private $plugin_version = null;

	/**
	 * Name of this plugin.
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_name = null;

	/**
	 * Unique identifier for this plugin.
	 *
	 *
	 * The variable is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_slug = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $settings_db_slug = null;

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $stored_settings = null;

	/**
	 * Allowed values for the position of the bar
	 *
	 *
	 * @since    3.0
	 *
	 * @var      array
	 */
	private $valid_positions = null;

	/**
	 * Allowed social networks
	 *
	 *
	 * @since    1.5
	 *
	 * @var      array
	 */
	private $valid_social_networks = null;

	/**
	 * Allowed headline HTML tags
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_headline_tags = null;

	/**
	 * Allowed icon families
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_icon_types = null;

	/**
	 * Allowed content alignments
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_content_alignments = null;

	/**
	 * Allowed font sizes
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_font_sizes = null;

	/**
	 * Allowed icon sizes
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_icon_sizes = null;

	/**
	 * Selected icon size
	 *
	 *
	 * @since    3.0.1
	 *
	 * @var      integer
	 */
	private $current_icon_size = null;

	/**
	 * Selected icon type
	 *
	 *
	 * @since    3.0.1
	 *
	 * @var      string
	 */
	private $current_icon_type = null;

	/**
	 * Allowed adjustment values
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_readjustments = null;

	/**
	 * Allowed vertical paddings
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_vertical_paddings = null;

	/**
	 * Allowed horizontal paddings
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_horizontal_paddings = null;

	/**
	 * Initial settings
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $default_settings = null;

	/**
	 * Plugin root URL
	 *
	 *
	 * @since    3.0.1
	 *
	 * @var      string
	 */
	private $plugin_root_url = null;

	/**
	 * Target of links
	 *
	 *
	 * @since    3.0.1
	 *
	 * @var      string
	 */
	private $link_target = null;

	/**
	 * Image file names with aspect ratios of the icons
	 *
	 *
	 * @since    3.0.1
	 *
	 * @var      array
	 */
	private $alt_aspect_ratios = null;

	/**
	 * Allowed maximal widths
	 *
	 *
	 * @since    4.1
	 *
	 * @var      array
	 */
	private $valid_max_viewport_widths = null;

	/**
	 * Allowed maximal widths
	 *
	 *
	 * @since    6.5.0
	 *
	 * @var      array
	 */
	private $valid_min_viewport_widths = null;

	/**
	 * Output Buffer Level
	 *
	 *
	 * @since    6.2
	 *
	 * @var      integer
	 */
	private $ob_level = -1;

	/**
	 * initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'apply_stored_settings' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// load contact bar near closing html body element with styles
		add_action( 'template_redirect', array( $this, 'activate_buffer' ), 537 );
		add_action( 'shutdown', array( $this, 'include_contact_bar' ), -537 );
		add_action( 'wp_head', array( $this, 'display_bar_styles' ) );
		//add_action( 'wp_footer', array( $this, 'print_script' ) );

		// set default values
		$this->plugin_version = '1.0.0';
		$this->plugin_name = 'Uli Contact Bar';
		$this->plugin_slug = 'uli-contact-bar';
		$this->plugin_root_url = plugin_dir_url( __FILE__ );
		$this->settings_db_slug = 'uli-contact-bar-options';
		$this->stored_settings = array();
		$this->valid_positions =  array( 'top', 'bottom' );
		$this->valid_social_networks = array( 'soundcloud', 'facebook', 'googleplus', 'twitter', 'skype', 'instagram', 'pinterest', 'youtube', 'snap', 'linkedin', 'xing', 'flickr', 'slideshare', 'tumblr', 'vimeo', 'imdb', 'yelp' );
		$this->valid_headline_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' );
		$this->valid_icon_types =  array( 'bright', 'dark' );
		$this->valid_content_alignments =  array( 'left', 'center', 'right' );
		$this->valid_font_sizes =  range( 4, 48 );
		$this->valid_icon_sizes =  range( 10, 48, 2 );
		$this->valid_readjustments =  range( 0, 75 );
		$this->valid_vertical_paddings =  range( 0, 32 );
		$this->valid_horizontal_paddings =  range( 0, 32 );
		$this->valid_max_viewport_widths =  array( 'never', '320px', '480px', '640px', '768px', '1024px' );
		$this->valid_min_viewport_widths =  array( 'never', '320px', '480px', '640px', '768px', '1024px' );
		$this->default_settings = array(
			'bg_color'				=> '#dfdfdf',
			'bg_color_opacity'		=> 1.0,
			'bg_transparent'		=> 0,
			'phone'					=> '',
			'phone_text'			=> '',
			'contact form'			=> '',
			'content_alignment'		=> $this->valid_content_alignments[ 1 ], // center
			'email'					=> 'me@email.com',
			'email_text'			=> '',
			'fixed'					=> 1,
			'font_size'				=> 15,
			'headline'				=> 'Contact to us',
			'headline_tag'			=> $this->valid_headline_tags[ 1 ], // h2
			'headline_url'			=> '',
			'horizontal_padding'	=> 15,
			'icon_size'				=> 30,
			'icon_type'				=> $this->valid_icon_types[ 1 ], // dark
			'keep_headline'			=> 0,
			'link_color'			=> '#0074A2',
			'max_viewport_width'	=> $this->valid_max_viewport_widths[ 0 ], // never
			'min_viewport_width'	=> $this->valid_min_viewport_widths[ 0 ], // never
			'open_new_window'		=> 0,
			'position'				=> $this->valid_positions[ 0 ], // top
			'readjustment'			=> 35,
			'show_headline'			=> 1,
			'show_labels'			=> 1,
			'show_shadow'			=> 1,
			'show_texts'			=> 0,
			'text_color'			=> '#333333',
			'vertical_padding'		=> 15,
		);
		// PNG image file names with aspect ratios (width / height)
		$this->alt_aspect_ratios = array(
			'imdb'			=> 2.115,
			'yelp'			=> 1.888,
			'soundcloud'	=> 1.0,
			'snap'			=> 1.0,
		);

		// hook on displaying a message after plugin activation
		// if single activation via link or bulk activation
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			$plugin_was_activated = get_transient( 'uli-contact-bar' );
			if ( false !== $plugin_was_activated ) {
				add_action( 'admin_notices', array( $this, 'display_activation_message' ) );
				delete_transient( 'uli-contact-bar' );
			}
		}
	}

	/**
	 * Set default values and load stored settings
	 *
	 * @since    6.5.0
	 */
	public function apply_stored_settings() {
		// get current or default settings
		$this->stored_settings = $this->get_stored_settings();

		// icon size
		$this->current_icon_size = $this->default_settings[ 'icon_size' ];
		if ( isset( $this->stored_settings[ 'icon_size' ] ) and in_array( $this->stored_settings[ 'icon_size' ], $this->valid_icon_sizes ) ) {
			$this->current_icon_size = $this->stored_settings[ 'icon_size' ];
		}

		// icon type
		$this->current_icon_type = $this->default_settings[ 'icon_type' ];
		if ( isset( $this->stored_settings[ 'icon_type' ] ) and in_array( $this->stored_settings[ 'icon_type' ], $this->valid_icon_types ) ) {
			$this->current_icon_type = $this->stored_settings[ 'icon_type' ];
		}

		// target of the links
		$this->link_target = '';
		if ( isset( $this->stored_settings[ 'open_new_window' ] ) and 1 == $this->stored_settings[ 'open_new_window' ] ) {
			$this->link_target = ' target="_blank"';
		}
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0
	 */
	private static function single_activate() {
		// store the flag into the db to trigger the display of a message after activation
		set_transient( 'uli-contact-bar', '1', 60 );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Set default settings
	 *
	 * @since    1.0
	 */
	private function set_default_settings() {
		if ( ! current_user_can( 'manage_options' ) )  {
			// use WordPress standard message for this case
			wp_die( esc_html__( 'You do not have sufficient permissions to manage options for this site.' ) );
		}

		// enhance the default settings
		$domain_name = preg_replace( '/^www\./', '', $_SERVER[ 'SERVER_NAME' ] );
		$this->default_settings[ 'email' ] = 'info@' . $domain_name;
		$this->default_settings[ 'headline' ] = __( 'Contact to us', 'uli-contact-bar' );

		// store default values in the db as a single and serialized entry
		update_option( $this->settings_db_slug, $this->default_settings );

		/**
		* to do: finish check
		* // test if the options are stored successfully
		* if ( false === get_option( $this->settings_db_slug ) ) {
		* 	// warn if there is something wrong with the options
		* 	something like: printf( '<div class="error"><p>%s</p></div>', esc_html__( 'The settings for plugin Purify WP Menus are not stored in the database. Is the database server ok?', 'purify_wp_menus' ) );
		* }
		*/
	}

	/**
	 * Returns whether user is on the login page
	 *
	 * @since    1.0
	 */
	private function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php', 'wp-signup.php' ) );
	}

	/**
	 * Returns whether user is on the login page
	 *
	 * @since    2.3
	 */
	private function esc_phonenumber ( $tel ) {

		// only strings
		if ( ! is_string( $tel ) ) {
			return '';
		}

		// remove invalid chars
		$tel = preg_replace( '/[^0-9a-z+]/i', '', $tel );

		// remove plus sign within the number, only keep it at the start
		$tel_first_sign = substr( $tel, 0, 1 );
		$tel_substr = substr( $tel, 1 );
		$tel_substr = preg_replace( '/[+]/', '', $tel_substr );
		$tel = $tel_first_sign . $tel_substr;

		// convert vanity numbers
		if ( preg_match( '/[a-z]/i', $tel ) ) {
			#$tel = preg_replace( '/ /', '0', $tel );
			$tel = preg_replace( '/[abc]/i', '2', $tel );
			$tel = preg_replace( '/[def]/i', '3', $tel );
			$tel = preg_replace( '/[ghi]/i', '4', $tel );
			$tel = preg_replace( '/[jkl]/i', '5', $tel );
			$tel = preg_replace( '/[mno]/i', '6', $tel );
			$tel = preg_replace( '/[pqrs]/i', '7', $tel );
			$tel = preg_replace( '/[tuv]/i', '8', $tel );
			$tel = preg_replace( '/[wxyz]/i', '9', $tel );
		}

		// E.164: maximum number of digits: 15
		$tel = substr( $tel, 0, 15 );

		// change country area sign, removed in 4.5.1 due to different country codes
		// $tel = preg_replace( '|^[+]|', '00', $tel );

		// return sanitized phone number
		return $tel;

	}

	/**
	 * For development: Display a var_dump() of the variable; die if true
	 *
	 * @since    1.0
	 */
	private function dump ( $v, $die = false ) {
		print "<pre>";
		var_dump( $v );
		print "</pre>";
		if ( $die ) die();
	} // dump()

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		$required_wp_version = '3.5';

		// check minimum version
		if ( ! version_compare( $GLOBALS['wp_version'], $required_wp_version, '>=' ) ) {
			// deactivate plugin
			deactivate_plugins( plugin_basename( __FILE__ ), false, is_network_admin() );
			// load language file for a message in the language of the WP installation
			self::load_plugin_textdomain();
			// stop WP request and display the message with backlink. Is there a proper way than wp_die()?
			$title = 'WordPress &rsaquo; Error';
			wp_die(
				// message in browser viewport
				sprintf(
					esc_html__( 'The plugin requires WordPress version %s or higher. Therefore, WordPress did not activate it. If you want to use this plugin update the Wordpress files to the latest version.', 'uli-contact-bar' ),
					$required_wp_version
				),
				// title in title tag
				esc_html__( $title ),
				array(
					// HTML status code returned
					'response'  => 200,
					// display a back link in the returned page
					'back_link' => true
				)
			);
		}

		if ( function_exists( 'is_multisite' ) and is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) and is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Return the plugin version.
	 *
	 * @since    1.5
	 *
	 * @return    Plugin version variable.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Return the plugin name.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the options slug in the WP options table.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_settings_db_slug() {
		return $this->settings_db_slug;
	}

	/**
	 * Return the supported social networks
	 *
	 * @since    1.5
	 *
	 * @return    Social networks variable.
	 */
	public function get_social_networks() {
		return $this->valid_social_networks;
	}

	/**
	 * Get current or default settings
	 *
	 * @since    1.0
	 */
	public function get_stored_settings() {
		// try to load current settings. If they are not in the DB return set default settings
		$settings = get_option( $this->settings_db_slug, array() );
		// sanitize: if key is not available, use default
		if ( is_array( $settings ) and ! empty( $settings ) ) {
			foreach( $this->default_settings as $key => $default_value ) {
				$settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : $default_value;
			}
		} else {
			// set default settings
			$this->set_default_settings();
			// try to load current settings again. Now there should be the data
			$settings = get_option( $this->settings_db_slug );
		}

		return $settings;
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), $this->plugin_version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), $this->plugin_version );
	}

	/**
	 * Activate output buffer
	 *
	 * @since    1.0
	 */
	public function activate_buffer() {
		// activate output buffer
		ob_start();
		// store the level of the output after opened
		$this->ob_level = ob_get_level();
	}

	/**
	 * Print the contact bar
	 *
	 * @since    1.0
	 */
	public function include_contact_bar() {
		// no bar on admin pages
		if ( $this->is_login_page() or is_admin() ) {
			return;
		}
		// correct link target for some links if desired
		if ( isset( $this->stored_settings[ 'open_new_window_social_only' ] ) and 1 == $this->stored_settings[ 'open_new_window_social_only' ] ) {
			$contact_target = '';
		} else {
			$contact_target = $this->link_target;
		}
		// get current buffer content and clean buffer
		$content = ob_get_contents();
		ob_clean();
		// esc_url() should be used on all URLs, including those in the 'src' and 'href' attributes of an HTML element.
		// open the bar
		$inject = '<div id="uli-wrapper"';

		// fixation of the bar
		if ( isset( $this->stored_settings[ 'fixed' ] ) and 1 == $this->stored_settings[ 'fixed' ] ) {
			$inject .= ' class="uli-fixed"';
		}
		$inject .= '>';

		// the headline
		$headline_tag = $this->default_settings[ 'headline_tag' ];
		if ( isset( $this->stored_settings[ 'headline_tag' ] ) and in_array( $this->stored_settings[ 'headline_tag' ], $this->valid_headline_tags ) ) {
			$headline_tag = $this->stored_settings[ 'headline_tag' ];
		}
		if ( isset( $this->stored_settings[ 'headline' ] ) and '' != $this->stored_settings[ 'headline' ] ) {

			if ( isset( $this->stored_settings[ 'headline_url' ] ) and '' != $this->stored_settings[ 'headline_url' ] ) {
				// headline as link
				$contact_list[] = sprintf(
				//$inject .= sprintf(
					'<li id="uli-title"><%s><a rel="nofollow" href="%s"%s>%s</a></%s></li>',
					$headline_tag,
					esc_url( $this->stored_settings[ 'headline_url' ] ),
					$contact_target,
					esc_html( $this->stored_settings[ 'headline' ] ),
					$headline_tag
				);
			} else {
				// headline as plain text
				//$inject .= sprintf(
				    $contact_list[] = sprintf(
					'<li id="uli-title"><%s>%s</%s></li>',
					$headline_tag,
					esc_html( $this->stored_settings[ 'headline' ] ),
					$headline_tag
				);
			}
		}



		// the phone numbers
		$devices = array(
			'phone'		=> __( 'Phone Number', 'uli-contact-bar' ),

		);
		foreach ( $devices as $device => $label ) {
			// the $device number
			if ( isset( $this->stored_settings[ $device ] ) and '' != $this->stored_settings[ $device ] ) {
				$device_text = $device . '_text';
				if ( isset( $this->stored_settings[ $device_text ] ) and '' != $this->stored_settings[ $device_text ] ) {
					$link_text = $this->stored_settings[ $device_text ];
				} else {
					$link_text = $this->stored_settings[ $device ];
				}
				$protocol = 'tel';
				if ( 'sms' == $device ) {
					$protocol = 'sms';
				}
				$link_href = $this->esc_phonenumber( $this->stored_settings[ $device ] );
				$contact_list[] = sprintf(
					'<li id="uli-%s"><a rel="nofollow" href="%s:%s">
					<span>%s</span></a></li>',
					$device,
					$protocol,
					$link_href,
					//$this->plugin_root_url,
					//$device,
					//$this->current_icon_type,
					//$this->current_icon_size,
					//$this->current_icon_size,
					//esc_attr( $label ),
					esc_html( $link_text )
				);
			}
		}


		/**
		 * Filter the personal contact informations in the contact bar as list items.
		 *
		 * @since 4.0
		 *
		 * @param array $contact_list An array of personal contact data as list items
		 */
		$contact_list = apply_filters( 'uli_contact_bar_data', $contact_list );

		// build the list
		if ( ! empty( $contact_list ) ) {
			// opens list
			$inject .= '<ul id="uli-directs">';
			// write the contact data item
			$inject .= implode( "", $contact_list );
			// closes list
			$inject .= '</ul>';
		}


		// close bar
		$inject .= '</div>';

		// escape regex char $
		$inject = preg_replace( '/\$/', '&#36;', $inject );

		// the position of the bar
		$position = $this->default_settings[ 'position' ];
		if ( isset( $this->stored_settings[ 'position' ] ) and in_array( $this->stored_settings[ 'position' ], $this->valid_positions ) ) {
			$position = $this->stored_settings[ 'position' ];
		}

		if ( isset( $this->stored_settings[ 'fixed' ] ) and 1 == $this->stored_settings[ 'fixed' ] ) {
			$property = 'padding-top';
			if ( 'bottom' == $position ) {
				$property = 'padding-bottom';
			}
			$inject .= '<script type="text/javascript">document.body.style.setProperty( "' . $property .'", document.getElementById(\'uli-wrapper\').offsetHeight+\'px\', "important" );</script>';
		}

		if ( 'bottom' == $position ) {
			// finds closing body element and add contact bar html code before it
			$content = preg_replace('/<\/[bB][oO][dD][yY]([^>]*)>/',"{$inject}</body$1>", $content );
		} else {
			// finds opening body element and add contact bar html code after it
			$content = preg_replace('/<[bB][oO][dD][yY]([^>]*)>/',"<body$1>{$inject}", $content );
		}

		// display it
		echo $content;

		// empty the buffer and print its content if opened by this plugin at activate_buffer()
		if ( -1 != $this->ob_level and $this->ob_level == ob_get_level() ) {
			ob_end_flush();
			//ob_flush();
		}
	}

	/**
	 * Print the customized CSS block
	 *
	 * @since    1.0
	 */
	public function display_bar_styles() {
		// the position of the bar
		$position = $this->default_settings[ 'position' ];
		if ( isset( $this->stored_settings[ 'position' ] ) and in_array( $this->stored_settings[ 'position' ], $this->valid_positions ) ) {
			$position = $this->stored_settings[ 'position' ];
		}

		// start bar styles
		$content  = '<style media="screen" type="text/css">';
		$content .= "\n";
		$content .= '#uli-wrapper ul,#uli-wrapper li,#uli-wrapper a, #uli-wrapper a span {display:inline;margin:0;padding:0;font-family:sans-serif;font-size:0.96em;line-height:1;}';
		$content .= ' #uli-wrapper ul:after,#uli-wrapper li:after {display:inline;}'; // fix for some themes to avoid icon lists in two lines
		$content .= ' #uli-wrapper li {margin:0 .5em;}';
		$content .= ' #uli-wrapper img {display:inline;vertical-align:middle;margin:0;padding:0;border:0 none;width:';
		$content .= $this->current_icon_size;
		$content .= 'px;height:';
		$content .= $this->current_icon_size;
		$content .= 'px;}';
		$content .= ' #uli-wrapper #uli-email {padding-right:1em;}';
		$content .= ' #uli-wrapper #uli-email a span, #uli-wrapper #uli-sms a span {margin: 0 .3em;}';
		$content .= ' #uli-wrapper li a span {white-space:nowrap;}';
		$content .= "\n";
		//$content .= '@media screen and (min-width:640px) {#uli-wrapper.uli-fixed {position:fixed;';
		$content .= '#uli-wrapper.uli-fixed {position:fixed;text-align:center;';
		$content .= $position;
		//$content .= ':0;left:0;z-index:2147483647;width:100%;}}';
		$content .= ':0;left:0;z-index:2147483647;width:100%;}';
		$content .= "\n";

		// visibility of bar in small displays
		if ( isset( $this->stored_settings[ 'max_viewport_width' ] ) and in_array( $this->stored_settings[ 'max_viewport_width' ], $this->valid_max_viewport_widths ) and 'never' != $this->stored_settings[ 'max_viewport_width' ] ) {
			$content .= sprintf( '@media screen and (max-width:%s) {#uli-wrapper {display:none;}}', $this->stored_settings[ 'max_viewport_width' ] );
			$content .= "\n";
		}

		// visibility of bar in big displays
		if ( isset( $this->stored_settings[ 'min_viewport_width' ] ) and in_array( $this->stored_settings[ 'min_viewport_width' ], $this->valid_min_viewport_widths ) and 'never' != $this->stored_settings[ 'min_viewport_width' ] ) {
			$content .= sprintf( '@media screen and (min-width:%s) {#uli-wrapper {display:none;}}', $this->stored_settings[ 'min_viewport_width' ] );
			$content .= "\n";
		}

		// if checked show email address and phone numbers in small displays
		if ( isset( $this->stored_settings[ 'show_texts' ] ) and 1 == $this->stored_settings[ 'show_texts' ] ) {
			// show them without inline breaks and one below the other
			$content .= '@media screen and (max-width:480px) {';
			$content .= '#uli-wrapper #uli-directs li {margin-bottom:.5em;display:block;}';
			$content .= ' #uli-wrapper ul {display:block;}';
			$content .= '}';
			$content .= ' #uli-wrapper #uli-directs a {white-space:nowrap;}';
			$content .= 'ul#uli-directs li{display:inline !important;}';
		}
		$content .= "\n";
		$content .= '@media screen and (max-width:480px) {#uli-wrapper #uli-directs {margin-bottom:.5em;} #uli-wrapper ul {display:block;}}';
		$content .= "\n";

		// fixation of bar
		if ( isset( $this->stored_settings[ 'fixed' ] ) and 1 == $this->stored_settings[ 'fixed' ] ) {
			/* space between bar and page content */
			$readjustment = $this->default_settings[ 'readjustment' ];
			if ( isset( $this->stored_settings[ 'readjustment' ] ) and in_array( $this->stored_settings[ 'readjustment' ], $this->valid_readjustments ) ) {
				$readjustment = $this->stored_settings[ 'readjustment' ];
			}
			$content .= sprintf( '@media screen and (min-width: 640px) { body { padding-%s: %dpx !important; } }', $position, $readjustment );
			$content .= "\n";
		}

		// styles of the bar and headline
		$bar_styles = '';

		$vertical_padding = $this->default_settings[ 'vertical_padding' ];
		if ( isset( $this->stored_settings[ 'vertical_padding' ] ) and in_array( $this->stored_settings[ 'vertical_padding' ], $this->valid_vertical_paddings ) ) {
			$vertical_padding = absint( $this->stored_settings[ 'vertical_padding' ] );
		}
		$horizontal_padding = $this->default_settings[ 'horizontal_padding' ];
		if ( isset( $this->stored_settings[ 'horizontal_padding' ] ) and in_array( $this->stored_settings[ 'horizontal_padding' ], $this->valid_horizontal_paddings ) ) {
			$horizontal_padding = absint( $this->stored_settings[ 'horizontal_padding' ] );
		}
		$bar_styles .= sprintf( ' padding: %spx %spx;', $vertical_padding, $horizontal_padding );

		if ( isset( $this->stored_settings[ 'bg_transparent' ] ) and 1 == $this->stored_settings[ 'bg_transparent' ] ) {
			$bar_styles .= ' background-color: transparent;';
		} else {
			$bg_color = $this->default_settings[ 'bg_color' ];
			if ( isset( $this->stored_settings[ 'bg_color' ] ) and '' != $this->stored_settings[ 'bg_color' ] ) {
				$bg_color = esc_attr( $this->stored_settings[ 'bg_color' ] );
			}
			// remove hash and split 6 characters to 3 pairs stored in $pairs if available
			if ( preg_match( '/^([a-zA-Z0-9]{2})([a-zA-Z0-9]{2})([a-zA-Z0-9]{2})$/', str_replace( '#', '', $bg_color ), $hexpairs ) ) {
				// convert hexadecimals to decimals
				$rgb = array_map( 'hexdec', $hexpairs );
				// prepare opacity value
				$bg_color_opacity = $this->default_settings[ 'bg_color_opacity' ];
				if ( isset( $this->stored_settings[ 'bg_color_opacity' ] ) and '' != $this->stored_settings[ 'bg_color_opacity' ] ) {
					$bg_color_opacity = $this->sanitize_0_to_1( $this->stored_settings[ 'bg_color_opacity' ] );
				}
				// set background color with opacity
				$bar_styles .= sprintf( ' background-color: rgba( %d, %d, %d, %1.3f );', $rgb[ 1 ], $rgb[ 2 ], $rgb[ 3 ], $bg_color_opacity );
			} else {
				$bar_styles .= sprintf( ' background-color: %s;', $bg_color );
			}
		}

		$text_color = $this->default_settings[ 'text_color' ];
		if ( isset( $this->stored_settings[ 'text_color' ] ) and '' != $this->stored_settings[ 'text_color' ] ) {
			$text_color = esc_attr( $this->stored_settings[ 'text_color' ] );
		}
		$bar_styles .= sprintf( ' color: %s;', $text_color );
		$headline_color = sprintf( ' color: %s;', $text_color );

		$content_alignment = $this->default_settings[ 'content_alignment' ];
		if ( isset( $this->stored_settings[ 'content_alignment' ] ) and in_array( $this->stored_settings[ 'content_alignment' ], $this->valid_content_alignments ) ) {
			$content_alignment = esc_attr( $this->stored_settings[ 'content_alignment' ] );
		}
		$bar_styles .= sprintf( ' text-align: %s;', $content_alignment );

		// show shadow if desired and dependent of the bar's position
		if ( isset( $this->stored_settings[ 'show_shadow' ] ) and 1 == $this->stored_settings[ 'show_shadow' ] ) {
			if ( 'bottom' == $position ) {
				$bar_styles .= ' box-shadow: 0 -1px 6px 3px #ccc;';
			} else {
				$bar_styles .= ' box-shadow: 0 1px 6px 3px #ccc;';
			}
		}
		$content .= sprintf( '#uli-wrapper {%s } ', $bar_styles );
		$content .= "\n";

		// styles of headline
		$headline_tag = $this->default_settings[ 'headline_tag' ];
		if ( isset( $this->stored_settings[ 'headline_tag' ] ) and in_array( $this->stored_settings[ 'headline_tag' ], $this->valid_headline_tags ) ) {
			$headline_tag = $this->stored_settings[ 'headline_tag' ];
		}
		$content .= sprintf( '#uli-wrapper %s { display: inline; margin: 0; padding: 0; font: normal normal bold 15px/1 sans-serif; ', $headline_tag );
		$content .= $headline_color;
		$content .= ' }';
		$content .= "\n";
		$content .= sprintf( '#uli-wrapper %s::before, %s::after { display: none; }', $headline_tag, $headline_tag );
		$content .= "\n";

		// hide headline in tablets and smartphones if desired
		if ( isset( $this->stored_settings[ 'keep_headline' ] ) and 0 == $this->stored_settings[ 'keep_headline' ] ) {
			$content .= sprintf( '@media screen and (max-width: 768px) { #uli-wrapper %s { display: none; } }', $headline_tag );
			$content .= "\n";
		}


		// color of links
		$link_color = $this->default_settings[ 'link_color' ];
		if ( isset( $this->stored_settings[ 'link_color' ] ) and '' != $this->stored_settings[ 'link_color' ] ) {
			$link_color = esc_attr( $this->stored_settings[ 'link_color' ] );
		}
		$content .= sprintf( '#uli-wrapper a { color: %s; } ', $link_color );
		$content .= "\n";

		// size of text
		$font_size = $this->default_settings[ 'font_size' ];
		if ( isset( $this->stored_settings[ 'font_size' ] ) and in_array( $this->stored_settings[ 'font_size' ], $this->valid_font_sizes ) ) {
			$font_size = $this->stored_settings[ 'font_size' ];
		}
		$content .= sprintf( '#uli-wrapper %s, #uli-wrapper ul, #uli-wrapper li, #uli-wrapper a, #uli-wrapper a span { font-size: %dpx; } ', $headline_tag, $font_size );
		$content .= "\n";

		// hide headline if desired
		if ( isset( $this->stored_settings[ 'show_headline' ] ) and 0 == $this->stored_settings[ 'show_headline' ] ) {
			$content .= sprintf( '#uli-wrapper %s { display: inline; left: -32768px; margin: 0; padding: 0; position: absolute; top: 0; z-index: 1000; } ', $headline_tag );
			$content .= "\n";
		}

		// custom sizes for icons with given aspect ratio
		foreach ( $this->valid_social_networks as $icon ) {
			if ( in_array( $icon, array_keys( $this->alt_aspect_ratios ) ) and isset( $this->stored_settings[ $icon ] ) and '' != $this->stored_settings[ $icon ] ) {
				$content .= sprintf(
					'#uli-wrapper #uli-%s img {width:%dpx;height:%dpx;}',
					$icon,
					intval( round( $this->alt_aspect_ratios[ $icon ] * $this->current_icon_size ) ),
					$this->current_icon_size
				);
				$content .= "\n";
			}// if (aspect ratio and icon)
		} // foreach(icon)

		// close style block
		$content .= '</style>';
		$content .= "\n";

		// add print style block
		$content .= '<style media="print" type="text/css">#uli-wrapper { display:none; }</style>';

		/**
		 * Filter the style sheet of the contact bar as string.
		 *
		 * @since 4.0
		 *
		 * @param string $content A string of CSS code
		 */
		$content = apply_filters( 'uli_contact_bar_style', $content );

		/* print css */
		echo $content;
	}

	/**
	 * Print a message after plugin activation
	 *
	 * @since    1.0
	 */
	public function display_activation_message () {

		$text = 'Settings';

		if ( is_rtl() ) {
			$sep = '&lsaquo;';
			// set link #2
			$link = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->plugin_slug ) ) ),
				$this->plugin_name,
				$sep,
				esc_html__( $text )
			);
		} else {
			$sep = '&rsaquo;';
			// set link #2
			$link = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->plugin_slug ) ) ),
				esc_html__( $text ),
				$sep,
				$this->plugin_name
			);
		}

		// set whole message
		printf(
			'<div class="updated notice is-dismissible"><p>%s</p></div>',
			sprintf(
				esc_html__( 'Welcome to the plugin %s! You can configure it at %s.', 'uli-contact-bar' ),
				$this->plugin_name,
				$link
			)
		);

	}

	/**
	 * Sanitize floats between 0 and 1
	 * return 1.0 by default
	 *
	 * @since    5.1
	 */
	private function sanitize_0_to_1 ( $v ) {
		if ( is_float( $v ) ) {
			// if value is a floating point number take it as is
			$f = $v;
		} elseif ( is_string( $v ) ) {
			// consider localized use of commas as decimal points and change them to points
			// and cast the result to a floating point number
			$f = floatval( str_replace( ',', '.', $v ) );
		} else {
			// in all other cases set to default value
			$f = $this->default_settings[ 'bg_color_opacity' ];
		}
		// check if value is between 0 and 1 and limit it if necessary
		// and return the sanitized value
		if ( $f < 0 ) {
			return 0.0;
		} elseif ( 1 < $f ) {
			return 1.0;
		} else {
			return $f;
		}
	}
}
