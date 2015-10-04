<?php
   /*
   Plugin Name: Qinvoice Signup
   Plugin URI: http://www.q-invoice.com
   Description: implements q-invoice signup form for app
   Version: 1.0
   Author: Casper Mekel
   Author URI: http://www.q-invoice.com
   License: GPL2
   */

if (!session_id()) {
    session_start();
}

function add_query_vars_filter_qs( $vars ){
  // for referrer 
  $vars[] = "r";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter_qs' );

if(isset($_GET['r'])){
	$_SESSION['qinvoice_referer'] = $_GET['r'];
}

include_once( 'includes/functions.php' );

if ( !class_exists( 'Qinvoice_Signup' ) ) {

	class Qinvoice_Signup {

		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;

		public $link;
		public $class;
		public $action;
		
		public $settings;

		/**
		 * Constructor
		 */
		public function __construct() {
			self::$plugin_prefix = 'wpqs_';
			self::$plugin_basefile = plugin_basename(__FILE__);
			self::$plugin_url = plugin_dir_url(self::$plugin_basefile);
			self::$plugin_path = trailingslashit(dirname(__FILE__));

			$this->options = get_option( 'qinvoice-signup-settings' );

		}
		
		/**
		 * Load the main plugin classes and functions
		 */
		public function includes() {
			
			include_once( 'includes/class.settings.php' );
		}

		/**
		 * Load the localisation 
		 */
		public function load_localisation() {	
			//return true;
			//echo dirname( self::$plugin_basefile );
			//load_plugin_textdomain( 'qinvoice-signup', false, 'languages' );
			load_plugin_textdomain( 'qinvoice-signup', false, dirname( self::$plugin_basefile ) . '/languages/' );
		}

		/**
		 * Load the hooks
		 */
		public function load() {
			// load the hooks
			add_action( 'plugins_loaded', array($this, 'load_localisation') );
			add_action( 'init', array( $this, 'load_hooks' ) );
			add_action( 'admin_init', array( $this, 'load_admin_hooks' ) );
		}
   		/**
		 * Add settings link to plugin page
		 */
		public function add_settings_link( $links ) {
			$settings = sprintf( '<a href="%s" title="%s">%s</a>' , admin_url( 'options-general.php?page=qinvoice-signup-settings' ) , __( 'Go to the settings page', 'qinvoice-signup' ) , __( 'Settings', 'qinvoice-signup' ) );
			array_unshift( $links, $settings );
			return $links;	
		}

			/**
		 * Load the init hooks
		 */
		public function load_hooks() {	

			$this->includes();
			
			$this->settings = new Qinvoice_Signup_Settings();
			$this->settings->load();
			

			wp_enqueue_style( "form-css", self::$plugin_url . "css/form.css" );
			wp_enqueue_style( "select2", self::$plugin_url . "scripts/select2/select2.css" );
		    // enqueue and localise scripts
		    wp_enqueue_script( "qinvoice", self::$plugin_url . "qinvoice.js", array( "jquery" ) );
		    wp_enqueue_script( "select2", self::$plugin_url . "scripts/select2/select2.js", array( "jquery" ) );
		    wp_enqueue_script( "strength", self::$plugin_url . "scripts/strength.js", array( "jquery" ) );
		    wp_enqueue_script( "qinvoice-signup", self::$plugin_url . "scripts/signup_form.js", array( "jquery" ) );

		    wp_localize_script('qinvoice-signup', 'qinvoice', array('url' => self::$plugin_url ));

				// wp_enqueue_script( plugin_dir_url( __FILE__ ) . 'script.js' );
			    //wp_localize_script( "my-ajax-handle", "the_ajax_script", array( "ajaxurl" => admin_url( "admin-ajax.php" ) ) );
		}
		
		/**
		 * Load the admin hooks
		 */
		public function load_admin_hooks() {
			
				add_filter( 'plugin_action_links_' . self::$plugin_basefile, array( $this, 'add_settings_link') );
			
		}

		
		public function big_form($formclass){
			$form = '<form id="signupForm" class="form_big '. $this->class .'" action="'. $this->action .'">
	  		<fieldset>

	  		<div class="form-row">
				<span class="">'. __('Enter your email','qinvoice-signup') .'<span class="error" data-type="email_invalid">'. __('Please provide a valid email address','qinvoice-signup') .'</span><span class="error" data-type="email_exists">'. __('Email address is already in use','qinvoice-signup') .'</span></span>
				<input type="text" name="s_email" id="s_email" class="input-block-level" tabindex="1" value=""/>
			</div>
			<div class="form-row">
				<span class="">'. __('Choose a password','qinvoice-signup') .'<span class="error" data-type="password_length">'. __('A password needs to be at least 5 characters','qinvoice-signup') .'</span></span>
				<input type="password" name="s_password" id="s_password" class="input-block-level" tabindex="2"/>
			</div>
			<input type="hidden" id="hide_password" value="'. __('Hide password','qinvoice-signup') .'">
			<input type="hidden" id="show_password" value="'. __('Show password','qinvoice-signup') .'">

			<div class="form-row">
				<span class="">'. __('Location of your business','qinvoice-signup') .'<span class="error"></span></span>
				<select name="s_country" id="s_country" class="input-block-level" tabindex="3" value=""/>
				'. countrySelect($profile->s_country,$this->options['language']) .'
				</select>
			</div>
	  		
	  		<div class="form-row">
	  			<input type="submit" tabindex="5" value="'. __('Create my account','qinvoice-signup') .' '. ($this->options['test_mode'] == 1 ? '[TEST MODE ENABLED]' : '').'" class="avia-button  avia-icon_select-no avia-color-custom avia-size-large avia-position-center" style="width: 100%; background-color:#2eb90e; border-color:#2eb90e; color:#ffffff; " id="doRegister">

	  		
	  			<p><small>'. sprintf(__('By signing up your agree to our <a href="%s">Terms and conditions</a>.','qinvoice-signup'),get_permalink(	$this->options['termsconditions_url'] ) ) .'</small></p>
	  		</div>

	  			
	  		</fieldset>
			<div style="display: none;">
		  		'. __('Leave this fields emtpy/unchanged!') .'
				<input type="hidden" name="h_time" value="'. time() .'"/>
				<input type="hidden" name="h_random" value="'. rand(111111,999999) .'"/>
				<input type="hidden" name="h_field" value=""/>
			</div>';



			$form .= '<input type="hidden" name="channel_id" value="'. ( ($_SESSION['qinvoice_referer'] > 0 && $this->options['enable_referrer'] == 1) ? $_SESSION['qinvoice_referer'] : $this->options['channel_id']) .'"/>';
			$form .= '<input type="hidden" name="p" value="'. @$_GET['p'] .'"/>';

			
			$form .= '<input type="hidden" name="s_language" value="'. $this->options['language'] .'"/>';
			$form .= '<input type="hidden" name="test_mode" value="'. $this->options['test_mode'] .'"/>';
			

	  		echo  '<input type="hidden" id="current_country" value="'. ip_info('','countrycode') .'">';
			$form .= '</form>';


			$success_msg = '<div id="successMessage"><hr/>';
			$success_msg .= '<h1>'. __('Welcome!') .'</h1>';
			$success_msg .= '<p>'. __('Your account has been successfully created. Please follow the link below to login using the emailaddress and password you used to register.','qinvoice-signup') .'</p>';
			$success_msg .= '<p></p>';
			$success_msg .= '<p><a href="'. $this->options['login_url'] .'">'. __('Click here to login','qinvoice-signup') .'</a></p>'; 
			$success_msg .= '<p>'. __('Thank you for using our service, would you have any questions do not hesitate to contact us.','qinvoice-signup') .'</p>';
			$success_msg .='</div>';

			return $form . $success_msg;
		}
		
	}
}	

/**
 * Instance of plugin
 */
$wpqs = new Qinvoice_Signup();
$wpqs->load();

	
	

  // add contact form
function qinvoice_signup_form($atts){
	global $wpqs;

	extract( shortcode_atts( array(
		'formclass' => 'std_class',
		'mode' => 'big',
		'termslink' => false,
		'action' => 'https://app.q-invoice.com/login.php'
	), $atts ) );

	
	
	$wpqs->class = $formclass;
	$wpqs->link = $termslink;
	$wpqs->action = $action;

	switch($mode){
		case 'horizontal':
			$form = $wpqs->horizontal_form($formclass);
		break;
		case 'big':
			$form = $wpqs->big_form($formclass);
		break;
		case 'sidebar':
			$form = $wpqs->sidebar_form($formclass);
		break;
	}
		

	$message = '<div style="display:none;" id="messageResult"><h3>'. __('Success!','qinvoice-signup') .'</h3>';
	$message .= '<p>'. __('Your account has been created. You are being redirect to the login page.','qinvoice-signup') .'</p>';
	$message .= '<p>'. __('Thank you for choosing q-invoice.','qinvoice-signup') .'</p>';
	$message .= '<p>'. sprintf(__('In case you aren\'t redirected. Please <a href="%s">click here</a>.','qinvoice-signup'),$action) .'</p>';
	$message .= '</div>';
	return $form . $message;
    }

add_shortcode("qinvoice_signup", "qinvoice_signup_form");


?>