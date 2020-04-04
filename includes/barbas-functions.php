<?php 
// Prevent direct access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Load text domain
function barbas_mail_load_textdomain() {
  load_plugin_textdomain( 'barbas-dwms', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

// Register sender
function barbas_mail_sender_register() {
	add_settings_section('barbas_mail_sender_section', __('Settings', 'barbas-dwms'), 'barbas_mail_sender_text', 'barbas_mail_sender');

	add_settings_field('barbas_mail_sender_id', __('Name','barbas-dwms'), 'barbas_mail_sender_function', 'barbas_mail_sender',  'barbas_mail_sender_section');

	register_setting('barbas_mail_sender_section', 'barbas_mail_sender_id');

	add_settings_field('barbas_mail_sender_email_id', __('Email', 'barbas-dwms'), 'barbas_mail_sender_email', 'barbas_mail_sender',  'barbas_mail_sender_section');

	register_setting('barbas_mail_sender_section', 'barbas_mail_sender_email_id');

}

// Sender functions
function barbas_mail_sender_function(){
	echo '<input name="barbas_mail_sender_id" type="text" class="regular-text" value="'.get_option('barbas_mail_sender_id').'" placeholder="Name"/>';
}

function barbas_mail_sender_email() {
	echo '<input name="barbas_mail_sender_email_id" type="email" class="regular-text" value="'.get_option('barbas_mail_sender_email_id').'" placeholder="no-reply@yourdomain.com"/>';
}


function barbas_mail_sender_text() {
	echo '<p>' .__( 'You may change your WordPress Default mail sender name and email.', 'barbas-dwms' ).'</p>';
}

function barbas_mail_sender_menu() {
	add_menu_page(__('Settings', 'barbas-dwms'), __('Barbas Mail Sender', 'barbas-dwms'), 'manage_options', 'barbas_mail_sender', 'barbas_mail_sender_output', 'dashicons-email-alt2');
}


// Form sender output
function barbas_mail_sender_output(){
?>	
	<?php settings_errors();?>
	<form action="options.php" method="POST">
		<?php do_settings_sections('barbas_mail_sender');?>
		<?php settings_fields('barbas_mail_sender_section');?>
		<?php submit_button();?>
	</form>
<?php }


// Change the default wordpress@ email address
function barbas_new_mail_from($old) {
	if (!empty(get_option('barbas_mail_sender_email_id'))){
		return get_option('barbas_mail_sender_email_id');
	}
	else{
		trigger_error (__('Barbas mail sender: The *sender email id* has not been set on', 'barbas-dwms').get_bloginfo('url').".", E_USER_NOTICE );
		return($old);
	}
}
function barbas_new_mail_from_name($old) {
	if (!empty(get_option('barbas_mail_sender_id'))){
		return get_option('barbas_mail_sender_id');
	}
	else{
		trigger_error (__('Barbas mail sender: The *sender id* has not been set on', 'barbas-dwms').get_bloginfo('url').".", E_USER_NOTICE );
		return($old);
	}
}
?>
