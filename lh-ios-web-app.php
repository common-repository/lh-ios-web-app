<?php
/*
Plugin Name: LH Ios Web App
Plugin URI: https://lhero.org/portfolio/lh-ios-web-app/
Description: Makes your wordpress site ios web app capable
Version: 2.00
Author: Peter Shaw
Author URI: https://shawfactor.com/
*/

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;


define ( 'LH_IOS_WEB_APP_PLUGIN_URL', plugin_dir_url(__FILE__)); // with forward slash (/).


class LH_ios_web_app_plugin {

// variables for the field and option names 
var $ios_app_web_app_capable_field_name = 'apple-mobile-web-app-capable';
var $ios_app_title_field_name = 'apple-mobile-web-app-title';
var $ios_app_apple_touch_startup_image = 'apple-touch-startup-image';
var $ios_app_apple_touch_icon ='apple-touch-icon';

var $site_icon_name = 'site_icon';

var $ios_app_maintain_state_field_name = 'js_helper-maintain_state';
var $ios_app_show_webapp_prompt_field_name = 'js_helper-show_addtohome_prompt';
var $hidden_field_name = 'lh_ios_web_app-submit_hidden';
var $opt_name = 'lh_ios_web_app-options';
var $namespace = 'lh_ios_web_app';

var $site_icon;
var $options;
var $filename;



function create_startup_image_sizes() {


$touch_icon_sizes[0] = array('height' => '460','width' => '320');
$touch_icon_sizes[1] = array('height' => '920','width' => '640');
$touch_icon_sizes[2] = array('height' => '960','width' => '640');
$touch_icon_sizes[3] = array('height' => '1096', 'width' => '640');

return $touch_icon_sizes;

}

function create_touch_icon_sizes() {

$startup_image_sizes[0] = array('height' => '57','width' => '57');
$startup_image_sizes[1] = array('height' => '72', 'width' => '72');
$startup_image_sizes[2] = array('height' => '114', 'width' => '114');
$startup_image_sizes[3] = array('height' => '144', 'width' => '144');

return $startup_image_sizes;

}


function get_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array( 
                                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                        );

                }

        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
}

function check_image_size($id,$size){

if( class_exists( 'Jetpack_Photon' )  ) {
remove_filter( 'image_downsize', array( Jetpack_Photon::instance(), 'filter_image_downsize' ) );
}

$imagedata = wp_get_attachment_image_src( $id, $size );


if( class_exists( 'Jetpack_Photon' )  ) {
add_filter( 'image_downsize', array( Jetpack_Photon::instance(), 'filter_image_downsize' ), 10, 3 );
}

if ($imagedata){

$size = $this->get_image_sizes($size);

if (($imagedata[1] == $size['width']) and ($imagedata[2] == $size['height'])){

return $imagedata[0];


}

} else {


return false;


}


}


function add_new_image_sizes_to_wp() {

foreach( $this->create_startup_image_sizes() as $size ){

add_image_size($this->ios_app_apple_touch_startup_image.'_'.$size['width'].'x'.$size['height'], $size['width'], $size['height'], true ); 


}

foreach( $this->create_touch_icon_sizes() as $size ){

add_image_size( $this->ios_app_apple_touch_icon.'_'.$size['width'].'x'.$size['height'], $size['width'], $size['height'], true ); 


}


}

function add_meta_to_head() {



echo "\n<!-- Start LH ios Web App -->\n";

//echo $this->site_icon;



echo "<meta name=\"viewport\" content=\"width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;\" />\n";

if   ($this->options[$this->ios_app_title_field_name]){

echo "<meta name=\"apple-mobile-web-app-title\" content=\"".$this->options[$this->ios_app_title_field_name]."\" />\n";

}

if   ($this->options[$this->ios_app_web_app_capable_field_name] == 1){

echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />\n";

}



foreach( $this->create_startup_image_sizes() as $size ){

if ($href = $this->check_image_size($this->options[$this->ios_app_apple_touch_startup_image], $this->ios_app_apple_touch_startup_image.'_'.$size['width'].'x'.$size['height'] )){

echo "<link rel=\"".$this->ios_app_apple_touch_startup_image."\" sizes=\"".$size['width']."x".$size['height']."\" href=\"".$href."\" />\n";

}

}

foreach( $this->create_touch_icon_sizes() as $size ){

if ($href = $this->check_image_size($this->site_icon, $this->ios_app_apple_touch_icon.'_'.$size['width'].'x'.$size['height'] )){

echo "<link rel=\"apple-touch-icon-precomposed\" sizes=\"".$size['width']."x".$size['height']."\" href=\"".$href."\" />\n";


}

}

echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\" />\n";

echo "<!-- End LH ios Web App -->\n\n";




}


function enqueue_scripts() {

if ($this->options[$this->ios_app_show_webapp_prompt_field_name] == 1){

wp_enqueue_script('ios_app-add_to_home_script', plugins_url( '/scripts/init_prompt.js' , __FILE__ ), array(), '1.1', true  );

}


if ($this->options[$this->ios_app_web_app_capable_field_name] == 1){

wp_enqueue_script('ios_app-web_app_capable', plugins_url( '/scripts/app_overrides.js' , __FILE__ ),array(), '1.1', true );


}


if ($this->options[$this->ios_app_maintain_state_field_name] == 1){

wp_enqueue_script('ios_app-maintain_state', plugins_url( '/scripts/app_state.js' , __FILE__ ),array(), '1.1', true );


}


}



// Now include admin GUI functions

// Prepare the media uploader
function add_admin_scripts(){

if (isset($_GET['page']) && $_GET['page'] == $this->filename) {
	// must be running 3.5+ to use color pickers and image upload
	wp_enqueue_media();
        wp_register_script('lh-ios-app-admin', LH_IOS_WEB_APP_PLUGIN_URL.'scripts/uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('lh-ios-app-admin');

}
}


function plugin_menu() {
add_options_page('LH Ios Web App Options', 'LH Ios Web App', 'manage_options', $this->filename, array($this,"plugin_options"));
}

function plugin_options() {

if (!current_user_can('manage_options')){

wp_die( __('You do not have sufficient permissions to access this page.') );

}




if( isset($_POST[ $this->hidden_field_name ]) && $_POST[ $this->hidden_field_name ] == 'Y' ) {

        // Read their posted value

$options[ $this->ios_app_web_app_capable_field_name ] = $_POST[ $this->ios_app_web_app_capable_field_name ];


if ($_POST[ $this->ios_app_title_field_name ] != ""){
$options[ $this->ios_app_title_field_name ] = sanitize_text_field($_POST[ $this->ios_app_title_field_name]);
}

if ($_POST[ $this->ios_app_apple_touch_startup_image."-url" ] != ""){
$options[ $this->ios_app_apple_touch_startup_image ] = $_POST[ $this->ios_app_apple_touch_startup_image ];
}

$options[ $this->ios_app_maintain_state_field_name ] = $_POST[ $this->ios_app_maintain_state_field_name ];

$options[ $this->ios_app_show_webapp_prompt_field_name ] = $_POST[ $this->ios_app_show_webapp_prompt_field_name ];


if (update_option( $this->opt_name, $options )){

$this->options = get_option($this->opt_name);

$fullsizepath = get_attached_file( $this->options[$this->ios_app_apple_touch_startup_image] );

wp_update_attachment_metadata( $this->options[$this->ios_app_apple_touch_startup_image], wp_generate_attachment_metadata( $this->options[$this->ios_app_apple_touch_startup_image], $fullsizepath ) );

$fullsizepath = get_attached_file( $this->site_icon );

wp_update_attachment_metadata( $this->site_icon, wp_generate_attachment_metadata( $this->site_icon, $fullsizepath ) );



?>
<div class="updated"><p><strong><?php _e('Ios web App settings saved', $this->namespace ); ?></strong></p></div>
<?php



}

}

echo "<h1>" . __( 'LH Ios Web App Settings', $this->namespace ) . "</h1>";

?>


<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y" />

<p><?php _e("Web App Capable:", $this->namespace); ?>
<select name="<?php echo $this->ios_app_web_app_capable_field_name; ?>" id="<?php echo $this->ios_app_web_app_capable_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->ios_app_web_app_capable_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->ios_app_web_app_capable_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>

<?php   if ($this->options[$this->ios_app_web_app_capable_field_name] == 1){   ?>

<p><?php _e('Title of your Web App: ', $this->namespace); ?>
<input type="text" name="<?php echo $this->ios_app_title_field_name; ?>" id="<?php echo $this->ios_app_title_field_name; ?>" value="<?php echo $this->options[$this->ios_app_title_field_name]; ?>"  />
(<a href="http://lhero.org/plugins/lh-ios-web-app/#<?php echo $this->ios_app_title_field_name; ?>">What does this mean?</a>)
</p>




<p><?php _e("Startup Image url: ", $this->namespace); ?> 
<input type="hidden" name="<?php echo $this->ios_app_apple_touch_startup_image; ?>"  id="<?php echo $this->ios_app_apple_touch_startup_image; ?>" value="<?php echo $this->options[$this->ios_app_apple_touch_startup_image]; ?>" size="10" />
<input type="url" name="<?php echo $this->ios_app_apple_touch_startup_image; ?>-url" id="<?php echo $this->ios_app_apple_touch_startup_image; ?>-url" value="<?php echo wp_get_attachment_url($this->options[$this->ios_app_apple_touch_startup_image]); ?>" size="50" />
<input type="button" class="button" name="<?php echo $this->ios_app_apple_touch_startup_image; ?>-upload_button" id="<?php echo $this->ios_app_apple_touch_startup_image; ?>-upload_button" value="Upload/Select Image" />
</p>


<p><?php _e("Maintain App State: ", $this->namespace); ?>
<select name="<?php echo $this->ios_app_maintain_state_field_name; ?>" id="<?php echo $this->ios_app_maintain_state_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->ios_app_maintain_state_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->ios_app_maintain_state_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
(<a href="http://lhero.org/plugins/lh-ios-web-app/#<?php echo $this->ios_app_maintain_state_field_name; ?>">What does this mean?</a>)
</p>


<p>
<?php _e("Show Web App Prompt:", $this->namespace); ?>
<select name="<?php echo $this->ios_app_show_webapp_prompt_field_name; ?>" id="<?php echo $this->ios_app_show_webapp_prompt_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->ios_app_show_webapp_prompt_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->ios_app_show_webapp_prompt_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
(<a href="https://lhero.org/plugins/lh-ios-web-app/#<?php echo $this->ios_app_show_webapp_prompt_field_name; ?>">What does this mean?</a>)
</p>

<?php  }  ?>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>

<?php



}

// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}

public function remove_apple_touch_site_icon_tag( $meta_tags ) {

foreach( $meta_tags as $meta_tag ) {

if (!strpos($meta_tag, "apple-touch-icon-precomposed")){

$tag[] = $meta_tag;

}

}
 
   return $tag;
}


public function on_activate() {

$fullsizepath = get_attached_file($this->site_icon);
$attach_data = wp_generate_attachment_metadata( $this->site_icon, $filename );
wp_update_attachment_metadata( $this->site_icon,  $attach_data );


}





function __construct() {

$this->options = get_option($this->opt_name);
$this->filename = plugin_basename( __FILE__ );
$this->site_icon = get_option($this->site_icon_name);

add_action( 'init', array($this,"add_new_image_sizes_to_wp"));
add_action('wp_head', array($this,"add_meta_to_head"));
add_action( 'wp_enqueue_scripts', array($this,"enqueue_scripts"));
add_action('admin_enqueue_scripts', array($this,"add_admin_scripts"));
add_action('admin_menu', array($this,"plugin_menu"));
add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);
add_filter( 'site_icon_meta_tags', array($this,"remove_apple_touch_site_icon_tag"), 10, 1);



}


}

$lh_ios_web_app_instance = new LH_ios_web_app_plugin();
register_activation_hook(__FILE__, array($lh_ios_web_app_instance,'on_activate') );





?>