<?php
/*
Plugin Name:    Transposh Widget Sticky
Description:    the plugin will create a sticky menu from selected widget (transposh plugin widget) on the theme.
Author:         Amy Smith
Version:        1.0.0
*/

function wpdocs_register_my_custom_menu_page(){
    add_menu_page( 
        __( 'Sticky Options', '' ),
        'sticky options',
        'manage_options',
        'cstickyoptions',
        'my_custom_menu_page',
        'dashicons-edit',
        70
    ); 
}
add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );
add_action('admin_init', 'sticky_init' );


function sticky_init(){
	register_setting('plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);
	add_settings_field('sticky_position', 'Position', 'setting_position', __FILE__, 'main_section');
    add_settings_field('sticky_css', 'CSS', 'setting_css', __FILE__, 'main_section');
    

}

function  section_text_fn() {
	echo '';
}

function  setting_position() {
	$options = get_option('plugin_options');
	$items = array("TOP LEFT", "TOP RIGHT", "BOTTOM LEFT", "BOTTOM RIGH");
	echo "<select id='drop_down1' name='plugin_options[sticky_position]'>";
	foreach($items as $item) {
		$selected = ($options['sticky_position']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function setting_css() {
	$options = get_option('plugin_options');
	echo "<textarea id='plugin_textarea_string' name='plugin_options[sticky_css]' rows='10' cols='50' type='textarea'>{$options['sticky_css']}</textarea>";
}


function my_custom_menu_page() {
?>
	<div class="wrap">
		<form action="options.php" method="post">
		<?php
			if ( function_exists('wp_nonce_field') ) 
				wp_nonce_field('plugin-name-action_' . "yep"); 
		?>
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

function plugin_options_validate($input) {
	$input['dropdown1'] =  wp_kses_allowed_html($input['dropdown1']);	
	return $input; 
}

function wpsites_register_widget() {
	register_sidebar( array(
    'name' => 'Sticky Menu',
    'id' => 'sticky-widget',
    'before_widget' => '<div>',
    'after_widget' => '</div>',
    ));
   }
   add_action( 'widgets_init', 'wpsites_register_widget' );



// Add scripts to wp_head()
function sticky_script() {

        if ( is_active_sidebar( 'sticky-widget' ) ) : 
        $options = get_option('plugin_options');
        $position = $options['sticky_position'];
        echo '<style type="text/css">';
        echo '.sticky_script {';
        switch($position) {
            case "TOP LEFT":
                echo "top: 10px; left: 10px;";
                break;
            case "TOP RIGHT":
                echo "top: 10px; right: 10px;";
                break;
            case "BOTTOM LEFT":
                echo "bottom: 10px; left: 10px;";
                break;
            case "BOTTOM RIGH":
                echo "bottom: 10px; right: 10px;";
                break;
            default:
            echo "";
        }
        echo '}';
        echo $options['sticky_css']; 
        echo '</style>';
        echo '<div class="sticky_script">';
            dynamic_sidebar( 'sticky-widget' ); 
        echo '</div>';
        endif;     
    };
add_action( 'wp_head', 'sticky_script' );