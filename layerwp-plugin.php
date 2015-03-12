<?php

/*
Plugin Name: Layers
Description: A WordPress site builder so simple and intuitive, you’ll be a pro the first time you use it.
Author: Franklin Gitonga
Plugin URI: http://radiumthemes.com/
Plugin URI: http://radiumthemes.com/
Version: 0.0.1
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/* 
Based on LayerWP by Obox Themes 
Author: Obox Themes
Author URI: http://www.oboxthemes.com/
Theme URI: http://demo.layerswp.com/
Version: 1.0.4
*/

/**
 * Init class for Layer WP.
 *
 * Loads all of the necessary components for the radium Gallery plugin.
 *
 * @since 1.0.0
 *
 * @package Layer WP
 * @author  Franklin Gitonga
 */
class LayerWP {

    /**
     * Current version of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.0.0';
	
	/**
	 * Current version of the database.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $db_version = '1';
	
    /** Magic *****************************************************************/

    /**
     * Layer WP uses many variables, several of which can be filtered to
     * customize the way it operates. Most of these variables are stored in a
     * private array that gets updated with the help of PHP magic methods.
     *
     * This is a precautionary measure, to avoid potential errors produced by
     * unanticipated direct manipulation of Layer WP's run-time data.
     *
     * @see layerwp::setup_globals()
     * @var array
     */
    private $data;

    /** Not Magic *************************************************************/
    
    /**
     * @var obj Add-ons append to this (Akismet, BuddyPress, etc...)
     */
    public $extend;

    /**
     * @var array Overloads get_option()
     */
    public $options      = array();

    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.0.0
     */
    public static function instance() {

        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been ran previously
        if ( null === $instance ) {
            $instance = new LayerWP;
            $instance->setup_globals();
            $instance->includes();
            $instance->init_classes();
        }

        // Always return the instance
        return $instance;

    }

    /** Magic Methods *********************************************************/

    /**
     * A dummy constructor to prevent Layer WP from being loaded more than once.
     *
     * @since 1.0.0
     * @see layerwp::instance()
     * @see layerwp();
     */
    private function __construct() { /* Do nothing here */ }

    /** Private Methods *******************************************************/

    /**
     * Set some smart defaults to class variables. Allow some of them to be
     * filtered to allow for early overriding.
     *
     * @since 1.0.0
     * @access private
     * @uses plugin_dir_path() To generate Layer WP plugin path
     * @uses plugin_dir_url() To generate Layer WP plugin url
     * @uses apply_filters() Calls various filters
     */
    private function setup_globals() {
		
		 /** Paths *************************************************************/
		
        // Setup some base path and URL information
        $this->file       			= __FILE__;
        $this->basename   			= apply_filters( 'layerwp_plugin_basenname', 	plugin_basename( $this->file ) );
        $this->plugin_dir 			= apply_filters( 'layerwp_plugin_dir_path',  	plugin_dir_path( $this->file ) );
        $this->plugin_url 			= apply_filters( 'layerwp_plugin_dir_url',   	plugin_dir_url ( $this->file ) );
	        
        /**
         * The current version of the theme. Use a random number for SCRIPT_DEBUG mode
         */
        define( 'LAYERS_VERSION', '1.0.0' );
        define( 'LAYERS_TEMPLATE_URI' , plugin_dir_url(__FILE__) );
        define( 'LAYERS_TEMPLATE_DIR' , plugin_dir_path(__FILE__) );
        define( 'LAYERS_THEME_TITLE' , 'Layers' );
        define( 'LAYERS_THEME_SLUG' , 'layers' );
        define( 'LAYERS_BUILDER_TEMPLATE' , 'builder.php' );

    }

    /**
     * Include required files
     *
     * @since 1.0.0
     * @access private
     * @uses is_admin() If in WordPress admin, load additional file
     */
    private function includes() {

        /**
         * @package Layers
         * 
         * Add define Layers constants to be used around Layers themes, plugins etc.
         *
         */
         
        /*
         * Load Widgets
         */
        require_once LAYERS_TEMPLATE_DIR . '/core/widgets/init.php';
        
        /*
         * Load Customizer Support
         */
        require_once LAYERS_TEMPLATE_DIR . '/core/customizer/init.php';
        
        /*
         * Load Custom Post Meta
         */
        require_once LAYERS_TEMPLATE_DIR . '/core/meta/init.php';
        
        /*
         * Load Widgets
         */
        require_once LAYERS_TEMPLATE_DIR . '/core/widgets/init.php';
        
        /*
         * Load Front-end helpers
         */
		require_once LAYERS_TEMPLATE_DIR . '/core/helpers/custom-fonts.php';	
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/extensions.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/post.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/post-types.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/sanitization.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/woocommerce.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/template.php';
        require_once LAYERS_TEMPLATE_DIR . '/core/helpers/integrate-template.php';
        
        /*
         * Load Admin-specific files
         */
        if( is_admin() ){
        	// Include form item class
        	require_once LAYERS_TEMPLATE_DIR . '/core/helpers/forms.php';
        
        	// Include design bar class
        	require_once LAYERS_TEMPLATE_DIR . '/core/helpers/design-bar.php';
        
        	// Include API class
        	require_once LAYERS_TEMPLATE_DIR . '/core/helpers/api.php';
        
        	// Include widget export/import class
        	require_once LAYERS_TEMPLATE_DIR . '/core/helpers/migrator.php';
        
        	//Load Options Panel
        	require_once LAYERS_TEMPLATE_DIR . '/core/options-panel/init.php';
        
        }

    }
 

    /**
     * Registers a plugin activation hook to make sure the current WordPress
     * version is suitable (>= 3.3.1) for use.
     *
     * @since 1.0.0
     *
     * @global int $wp_version The current version of this particular WP instance
     */
    public function activation() {

        global $wp_version;

        if ( version_compare( $wp_version, '3.0.0', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( printf( __( 'Sorry, but your version of WordPress, <strong>%s</strong>, does not meet the Layer WP\'s required version of <strong>3.3.1</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>', 'layerwp' ), $wp_version, admin_url() ) );
        }

    }

    /**
     * Loads the plugin classes
     *
     * @since 1.0.0
     */
    public function init_classes() {
	
		if ( is_admin() ) {
			
			new LayerWP_Template;
			
		}
		
    }

    /**
     * loads the frontend core assets.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {

    }

    /**
     * Load the translation file for current language. Checks the languages
     * folder inside the Layer WP plugin first, and then the default WordPress
     * languages folder.
     *
     * Note that custom translation files inside the Layer WP plugin folder
     * will be removed on Layer WP updates. If you're creating custom
     * translation files, please use the global language folder.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'plugin_locale' with {@link get_locale()} value
     * @uses load_textdomain() To load the textdomain
     */
    public function load_textdomain() {

        // Traditional WordPress plugin locale filter
        $locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
        $mofile        = $locale . '.mo';

        // Setup paths to current locale file
        $mofile_local  = $this->lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/plugins/layerwp/'. $mofile;
						
        // Look in global /wp-content/languages/gallery-central folder
        load_textdomain( $this->domain, $mofile_global );

        // Look in local /wp-content/plugins/gallery-central/ folder
        load_textdomain( $this->domain, $mofile_local );

        // Look in global /wp-content/languages/plugins/
        load_plugin_textdomain( $this->domain );
    }

    /**
     * Getter method for retrieving the url.
     *
     * @since 1.0.0
     */
    public static function get_url() {

        return plugins_url('', __FILE__);;

    }

    /**
     * Getter method for retrieving the url.
     *
     * @since 1.0.0
     */
    public static function get_dir() {

        return plugin_dir_path(__FILE__);;

    }

    /**
     * Getter method for retrieving the main plugin file path.
     *
     * @since 1.0.0
     */
    public static function get_file() {

        return self::$file;

    }

}

/**
 * The main function responsible for returning the one true Layer WP Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $layerwp = layerwp(); ?>
 *
 * @since 1.0.0
 *
 * @return The one true Layer WP Instance
 */
function layerwp() {

    $instance = LayerWP::instance();

    return $instance;

}

/**
 * Hook LayerWP early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Layer WP, to get their
 * actions, filters, and overrides setup without LayerWP being in the way.
 */
//if ( defined( 'LAYERWP_LATE_LOAD' ) ) {

    add_action( 'setup_theme', 'layerwp' );

//} else {

 //   layerwp();

//}
// End class


if( ! function_exists( 'layers_setup' ) ) {
	function layers_setup(){
		global $pagenow;

		/**
		 * Add support for widgets inside the customizer
		 */
		add_theme_support('widget-customizer');
 
		/**
		* Welcome Redirect
		*/
		if( isset($_GET["activated"]) && $pagenow = "themes.php" ) { //&& '' == get_option( 'layers_welcome' )

			update_option( 'layers_welcome' , 1);

			wp_safe_redirect( admin_url('admin.php?page=' . LAYERS_THEME_SLUG . '-get-started'));
		}

	} // function layers_setup
} // if !function layers_setup
add_action( 'after_setup_theme' , 'layers_setup', 10 );

/**
*  Enqueue front end styles and scripts
*/
if( ! function_exists( 'layers_register_standard_sidebars' ) ) {
	function layers_register_standard_sidebars(){
		/**
		 * Register Standard Sidebars
		 */
		register_sidebar( array(
			'id'		=> LAYERS_THEME_SLUG . '-off-canvas-sidebar',
			'name'		=> __( 'Mobile Sidebar' , 'layerswp' ),
			'description'	=> __( 'This sidebar will only appear on mobile devices.' , 'layerswp' ),
			'before_widget'	=> '<aside id="%1$s" class="content widget %2$s">',
			'after_widget'	=> '</aside>',
			'before_title'	=> '<h5 class="section-nav-title">',
			'after_title'	=> '</h5>',
		) );

		register_sidebar( array(
			'id'		=> LAYERS_THEME_SLUG . '-left-sidebar',
			'name'		=> __( 'Left Sidebar' , 'layerswp' ),
			'before_widget'	=> '<aside id="%1$s" class="content well push-bottom-large widget %2$s">',
			'after_widget'	=> '</aside>',
			'before_title'	=> '<h5 class="section-nav-title">',
			'after_title'	=> '</h5>',
		) );

		register_sidebar( array(
			'id'		=> LAYERS_THEME_SLUG . '-right-sidebar',
			'name'		=> __( 'Right Sidebar' , 'layerswp' ),
			'before_widget'	=> '<aside id="%1$s" class="content well push-bottom-large widget %2$s">',
			'after_widget'	=> '</aside>',
			'before_title'	=> '<h5 class="section-nav-title">',
			'after_title'	=> '</h5>',
		) );

		/**
		 * Register Footer Sidebars
		 */
		for( $footer = 1; $footer < 5; $footer++ ) {
			register_sidebar( array(
				'id'		=> LAYERS_THEME_SLUG . '-footer-' . $footer,
				'name'		=> __( 'Footer ', 'layerswp' ) . $footer,
				'before_widget'	=> '<section id="%1$s" class="widget %2$s">',
				'after_widget'	=> '</section>',
				'before_title'	=> '<h5 class="section-nav-title">',
				'after_title'	=> '</h5>',
			) );
		} // for footers

		/**
		 * Register WooCommerce Sidebars
		 */
		if( class_exists( 'WooCommerce' ) ) {
			register_sidebar( array(
				'id'        => LAYERS_THEME_SLUG . '-left-woocommerce-sidebar',
				'name'      => __( 'Left Shop Sidebar' , 'layerswp' ),
				'description'   => __( '' , 'layerswp' ),
				'before_widget' => '<aside id="%1$s" class="content well push-bottom-large widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h5 class="section-nav-title">',
				'after_title'   => '</h5>',
			) );
			register_sidebar( array(
				'id'        => LAYERS_THEME_SLUG . '-right-woocommerce-sidebar',
				'name'      => __( 'Right Shop Sidebar' , 'layerswp' ),
				'description'   => __( '' , 'layerswp' ),
				'before_widget' => '<aside id="%1$s" class="content well push-bottom-large widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h5 class="section-nav-title">',
				'after_title'   => '</h5>',
			) );
		}
	}
}
add_action( 'widgets_init' , 'layers_register_standard_sidebars' , 50 );

/**
*  Enqueue front end styles and scripts
*/
if( ! function_exists( 'layers_scripts' ) ) {
	function layers_scripts(){

		/**
		* Front end Scripts
		*/

		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-plugins-js' ,
			plugin_dir_url( __FILE__ ) . '/assets/frontend/js/plugins.js',
			array(
				'jquery',
			),
			LAYERS_VERSION
		); // Sticky-Kit

		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-framework-js' ,
			plugin_dir_url( __FILE__ ) . '/assets/frontend/js/layers.framework.js',
			array(
				'jquery',
			),
			LAYERS_VERSION,
			true
		); // Framework


		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		} // Comment reply script

		/**
		* Front end Styles
		*/

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-framework' ,
			plugin_dir_url( __FILE__ ) . '/assets/frontend/css/framework.css',
			array() ,
			LAYERS_VERSION
		);

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-components',
			plugin_dir_url( __FILE__ ) . '/assets/frontend/css/components.css',
			array(),
			LAYERS_VERSION
		); // Compontents

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-responsive',
			plugin_dir_url( __FILE__ ) . '/assets/frontend/css/responsive.css',
			array(),
			LAYERS_VERSION
		); // Responsive

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-icon-fonts',
			plugin_dir_url( __FILE__ ) . '/assets/frontend/css/layers-icons.css',
			array(),
			LAYERS_VERSION
		); // Icon Font

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-style' ,
			get_stylesheet_uri(),
			array() ,
			LAYERS_VERSION
		);

		if( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style(
				LAYERS_THEME_SLUG . '-woocommerce',
				plugin_dir_url( __FILE__ ) . '/assets/frontend/css/woocommerce.css',
				array(),
				LAYERS_VERSION
			); // Woocommerce
		}

		if( is_admin_bar_showing() ) {
			wp_enqueue_style(
				LAYERS_THEME_SLUG . '-admin',
				plugin_dir_url( __FILE__ ) . 'assets/admin/css/icons.css',
				array(),
				LAYERS_VERSION
			); // Admin CSS
		}

	}
}
add_action( 'wp_enqueue_scripts' , 'layers_scripts' );

/**
*  Enqueue admin end styles and scripts
*/
if( ! function_exists( 'layers_admin_scripts' ) ) {
	function layers_admin_scripts(){
		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-admin',
			plugin_dir_url( __FILE__ ) . 'assets/admin/css/admin.css',
			array(),
			LAYERS_VERSION
		); // Admin CSS

		wp_enqueue_style(
			LAYERS_THEME_SLUG . '-admin-editor',
			plugin_dir_url( __FILE__ ) . 'assets/admin/css/editor.min.css',
			array(),
			LAYERS_VERSION
		); // Inline Editor

		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-admin-editor' ,
			plugin_dir_url( __FILE__ ) . 'assets/admin/js/editor.min.js' ,
			array( 'jquery' ),
			LAYERS_VERSION,
			true
		); // Inline Editor

		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-admin-migrator' ,
			plugin_dir_url( __FILE__ ) . 'assets/admin/js/migrator.js' ,
			array(
				'media-upload'
			),
			LAYERS_VERSION,
			true
		);

		wp_localize_script(
			LAYERS_THEME_SLUG . '-admin-migrator',
			'migratori18n',
			array(
				'loading_message' => __( 'Be patient while we import the widget data and images.' , 'layerswp' ),
				'complete_message' => __( 'Import Complete' , 'layerswp' ),
				'importing_message' => __( 'Importing Your Content' , 'layerswp' ),
				'duplicate_complete_message' => __( 'Edit Your New Page' , 'layerswp' )
			)
		);// Migrator// Localize Scripts
		wp_localize_script(
			LAYERS_THEME_SLUG . '-admin-migrator',
			"layers_migrator_params",
			array(
					'duplicate_layout_nonce' => wp_create_nonce( 'layers-migrator-duplicate' ),
					'import_layout_nonce' => wp_create_nonce( 'layers-migrator-import' ),
					'preset_layout_nonce' => wp_create_nonce( 'layers-migrator-preset-layouts' ),
				)
		);

		// Onboarding Process
		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-admin-onboarding' ,
			plugin_dir_url( __FILE__ ) . 'assets/admin/js/onboarding.js',
			array(
					'jquery'
				),
			LAYERS_VERSION,
			true
		); // Onboarding JS

		wp_localize_script(
			LAYERS_THEME_SLUG . '-admin-onboarding' ,
			"layers_onboarding_params",
			array(
				'preset_layout_nonce' => wp_create_nonce( 'layers-migrator-preset-layouts' ),
				'update_option_nonce' => wp_create_nonce( 'layers-onboarding-update-options' ),
			)
		); // Onboarding ajax parameters

		wp_localize_script(
			LAYERS_THEME_SLUG . '-admin-onboarding' ,
			'onboardingi18n',
			array(
				'step_saving_message' => __( 'Saving...' , 'layerswp' ),
				'step_done_message' => __( 'Done!' , 'layerswp' )
			)
		); // Onboarding localization

		wp_enqueue_script(
			LAYERS_THEME_SLUG . '-admin' ,
			plugin_dir_url( __FILE__ ) . 'assets/admin/js/admin.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'wp-color-picker',
			),
			LAYERS_VERSION,
			true
		); // Admin JS

		wp_localize_script(
			LAYERS_THEME_SLUG . '-admin' ,
			"layers_admin_params",
			array(
				'backup_pages_nonce' => wp_create_nonce( 'layers-backup-pages' ),
				'backup_pages_success_message' => __('Your pages have been successfully backed up!', 'layerswp' )
			)
		); // Onboarding ajax parameters

		wp_enqueue_media();

	}
}

add_action( 'customize_controls_print_footer_scripts' , 'layers_admin_scripts' );
add_action( 'admin_enqueue_scripts' , 'layers_admin_scripts' );
 