<?php
/**
 * Plugin Name: Rapid Theme Quickstart
 * Plugin URI: http://devcabin.com
 * Description: Enqueues bootstrap and jQuery, adds footer JS options, Slick slider, dynamic meta description, TGM plugin activator, IE fixes, modernizr, adds thumbnail support.
 * Version:  0.1.5
 * Author: George Featherstone
 * Author URI: http://devcabin.com
 * Text Domain: s_theme_plugin
 * License: GPL2
   Copyright 2015  @thedarklit  (email : george@devcabin.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Custom Theme Options
function devc_theme_admin_init() {
     register_setting('devc_options', 'devc_theme_options');
}

function setup_theme_admin_menus() {
    add_menu_page('Theme settings', 'devcabin Options', 'manage_options', 
        'devcabin_theme_settings', 'theme_front_page_settings');
         
    add_submenu_page('devcabin_theme_settings', 
        'Front Page Elements', 'Front Page', 'manage_options', 
        'devcabin_theme_settings', 'theme_front_page_settings'); 
}

function theme_front_page_settings() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>

<div class="wrap">
        <?php screen_icon('themes'); ?> 
        <h2>devcabin Custom Theme Settings</h2>
<form method="post" action="options.php">
    <?php 
    // Load the options from the WP db
    $options = get_option('devc_theme_options');
    // WP built-in function to display the appropriate fields for saving options
    settings_fields("devc_options"); ?>

    
    <h3>Footer area scripts</h3>
    <table class="form-table">

		<tr>
			<!--th scope="row">Open external links in a new window? Enter YES or NO</th-->
			<th scope="row">Anything you paste in here shows up in a "script" tag in the footer of the theme.<br />
			Initial values include basic setup for a Slick slider and the jQuery <br />
			to open external links in a new window.</th>
	    </tr>
		
	<td>
	<textarea rows="20" cols="50" name="devc_theme_options[ext_links]" value="<?php echo stripslashes($options["ext_links"]); ?>" />
	
//slider config	
// Details at http://kenwheeler.github.io/slick/

	$('.new-slider').slick({
			autoplay: true
	});// do not put in doc.ready function

$(document).ready(function(){
// open external links in new windows
		$('a').each(function() {
			var a = new RegExp('/' + window.location.host + '/');
			if(!a.test(this.href)) {
				$(this).click(function(event) {
					event.preventDefault();
					event.stopPropagation();
					window.open(this.href, '_blank');
				});
			}
		});
}); // END $(document).ready
	
	</textarea>
	
	</td>
		</tr>
               
    </table>
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>  

</form>
<style>
.form-table th {
	 padding: 8px 10px 20px 0;
}
</style>
<!--
<h3>Validation</h3>

<?php /*
$thechoice = $options["ext_links"];

if ($thechoice == "YES" || $thechoice == "Yes" || $thechoice == "yes") { ?>
<h2>So glad you chose Yes!</h2>
<?php } elseif ($thechoice == "NO" || $thechoice == "No" || $thechoice == "no") {  ?>
<h2>That's cool, we won't do it.</h2>
<?php } elseif ($options["ext_links"] == "POO") {  ?>
<h2>Dude, that's gross. </h2>
<?php } else {  ?>
<h2 class="warning">Come on, you have to say YES or NO or it won't work.</h2>
<?php } */ ?>
<style>
.warning {
	background:yellow;
	padding:30px;
	color:#000;
}
</style>
-->
</div>



<?php
}
	
add_action("admin_init", "devc_theme_admin_init");
add_action("admin_menu", "setup_theme_admin_menus");



// Add stuff to wp_head()
function s_theme_head_stuff() { 
    if (is_single() || is_page() ) : if ( have_posts() ) : while ( have_posts() ) : the_post(); 
		$full_content = get_the_content(); // the_content minus formatting
		$new_content = strip_tags($full_content); // remove any formatting left
?>
<meta name="description" content="<?php wp_title(); ?> - echo substr($new_content, 0, 60);?> - <?php bloginfo('name'); ?>"  /> 
	<?php endwhile; endif; elseif(is_home()) : ?>
<meta name="description" content="<?php wp_title(); ?> - <?php bloginfo('description'); ?>" />
	<?php endif; ?>
<?php // Make cool modern stuff compatible with crappy old browsers ?>	
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
   <script type='text/javascript' src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
   <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
<![endif]-->

<?php }
add_action( 'wp_head', 's_theme_head_stuff' );


// Add stuff to wp_footer()
function s_theme_footer_stuff() { 
?>
<script type="text/javascript">

<?php
	$options = get_option('devc_theme_options');
	echo stripslashes($options["ext_links"]); 
?>	

</script>
<?php } 

add_action( 'wp_footer', 's_theme_footer_stuff' , 30 );
	

function s_scripts_method() {
	
	wp_enqueue_style( 's_theme-bs-style', plugins_url( '/inc/css/bootstrap.min.css', __FILE__ ) );
	wp_enqueue_style( 's_theme-bs-theme', plugins_url( '/inc/css/bootstrap-theme.min.css', __FILE__ ) );
	// Slick (you know, the "last slider you'll ever need")
	wp_enqueue_style( 's_theme-slick-style', plugins_url( '/inc/css/slick.css', __FILE__ ) );

	// Now for the Scripts
	
	// Add jQuery the right way: http://css-tricks.com/snippets/wordpress/include-jquery-in-wordpress-theme/
	if (!is_admin()) add_action("wp_enqueue_scripts", "devc_jquery_enqueue", 11);
	function devc_jquery_enqueue() {
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", false, null);
	   wp_enqueue_script('jquery');
	}
	
	// Bootstrap scripts just in case
	wp_enqueue_script(
		's_theme-bootstrap-min',
		plugins_url( '/inc/js/bootstrap.min.js', __FILE__ ),
		array( 'jquery' )
	);
	
	// Slick slider, also after jquery
	wp_enqueue_script(
		's_theme-slick-slider',
		plugins_url( '/inc/js/slick.min.js', __FILE__ ),
		array( 'jquery' )
	);		

	// And you gotta have modernizr
	wp_enqueue_script( 's_theme-modernizr', plugins_url( '/inc/js/modernizr.js', __FILE__ ) );

}

add_action( 'wp_enqueue_scripts', 's_scripts_method' );
add_theme_support( 'post-thumbnails' );




/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @package	   TGM-Plugin-Activation
 * @subpackage Example
 * @version	   2.3.6
 * @author	   Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author	   Gary Jones <gamajo@gamajo.com>
 * @copyright  Copyright (c) 2012, Thomas Griffin
 * @license	   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
 
// require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
require_once  dirname( __FILE__ ) . '/inc/class-tgm-plugin-activation.php';


add_action( 'tgmpa_register', 'devc_theme_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function devc_theme_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin pre-packaged with a theme
		/*
		array(
			'name'     				=> 'Wordpress Creation Kit PRO', // The plugin name
			'slug'     				=> 'wordpress-creation-kit-pro', // The plugin slug (typically the folder name)
			'source'   				=> get_stylesheet_directory() . '/plugins/wordpress-creation-kit-pro_2.0.6.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '2.0.6', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
               */
		// This is an example of how to include a plugin from the WordPress Plugin Repository
		
		array(
			'name' 		=> 'Acunetix WP Security',
			'slug' 		=> 'wp-security-scan',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'Limit Login Attempts',
			'slug' 		=> 'limit-login-attempts',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'Google XML Sitemaps',
			'slug' 		=> 'google-sitemap-generator',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'InfiniteWP Client',
			'slug' 		=> 'iwp-client',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'Black Studio TinyMCE Widget',
			'slug' 		=> 'black-studio-tinymce-widget',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'ACF',
			'slug' 		=> 'advanced-custom-fields',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'WCK Custom Post Types',
			'slug' 		=> 'wck-custom-fields-and-custom-post-types-creator',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'Search Everything',
			'slug' 		=> 'search-everything',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'Contact Form 7',
			'slug' 		=> 'contact-form-7',
			'required' 	=> false,
		),
		

	);

	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 's_theme_plugin';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
			'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
			'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );

}








/*
SO the plan ...

Pull in bootstrap - DONE
Add plugin activator (optional I suppose)
Modernizr - DONE
Slick Slider (all files!) - DONE
add_theme_support( 'post-thumbnails' ); - DONE
En Q jQuery -DONE

Function string limit words - DONE

Dynamic meta into head - and apparently title now too
Also the backward compatible shims - DONE

Footer scripts (slider and search word changer - must be editable!!)
- DONE

WISH LIST - pre-launch checklist

Possibly some code snippet notes? maybe hide-able like the checklist?

Maybe add the full meta to single

+++++++++++++++++++++++++++++
After hack email 1/16/15
+++++++++++++++++++++++++++++


    Restore any affected files to an earlier date (this is only a temporary fix).
    Change all of your passwords, including FTP Manager, Admin pages, etc.
    Make sure you have the most updated version of WordPress and any plugins you are using.
    Temporarily disable all plugins.
    Temporarily disable all comments. A simple way is to upload this plugin: Disable Comments for WordPress. Make sure you activate it.
    Install the WordPress Firewall plugin. Activate it.
    Install the WP Security Scan plugin. Activate it. Let it scan your site and then you should fix any errors or problems that it shows you.
    Add a .htaccess file to your wp-admin folder for extra security.
    Then password protect your wp-admin folder (using the “Permissions” menu option)
    Change the permissions on your wp-config.php file, all of your .htaccess files, and all index.php files to add extra security. (Uncheck the “write” box in the “Permissions” menu option).
    Double check your files again to make sure that the virus hasn’t returned in the time it took you to make the above changes. If it has returned, remove the virus code and save the changes, making sure not to undo any of the permission changes you just made.
    Monitor your site for the next 24-48 hours before you know for sure that it’s gone. Check on your website every 2-6 hours to see if the virus pops up again.
    Request reconsideration of your site once you know that the virus is gone.
    Backup ALL your files
    Keep the Firewall plugin and the Security Scan plugins enabled to block any future attacks.



*/
