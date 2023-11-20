<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

// Load text domain
add_action('init', 'barbas_mail_load_textdomain');
function barbas_mail_load_textdomain()
{
	load_plugin_textdomain('barbas-dwms', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Check PHP version and display an error message for older versions.
add_action('admin_notices', 'barbas_mail_php_version_notice');
function barbas_mail_php_version_notice()
{
	// Check if PHP version is less than 5.6
	if (version_compare(phpversion(), '5.6', '<')) {
		echo '<div class="error"><p>' . __('The Barbas Mail Sender plugin requires PHP version 5.6 or higher.', 'barbas-dwms') . '</p></div>';
	}
}

// Register sender settings
add_action('admin_init', 'barbas_mail_sender_register');
function barbas_mail_sender_register()
{
	// Add settings section
	add_settings_section('barbas_mail_sender_section', __('Settings', 'barbas-dwms'), 'barbas_mail_sender_text', 'barbas_mail_sender');

	// Add settings fields for sender name and email
	add_settings_field('barbas_mail_sender_id', __('Name', 'barbas-dwms'), 'barbas_mail_sender_function', 'barbas_mail_sender',  'barbas_mail_sender_section');
	register_setting('barbas_mail_sender_section', 'barbas_mail_sender_id');

	add_settings_field('barbas_mail_sender_email_id', __('Email', 'barbas-dwms'), 'barbas_mail_sender_email', 'barbas_mail_sender',  'barbas_mail_sender_section');
	register_setting('barbas_mail_sender_section', 'barbas_mail_sender_email_id');
}

// Render sender settings fields
function barbas_mail_sender_function()
{
	// Display input field for sender name
	echo '<input name="barbas_mail_sender_id" type="text" class="regular-text" value="' . get_option('barbas_mail_sender_id', '') . '" placeholder="Name"/>';
}

function barbas_mail_sender_email()
{
	// Display input field for sender email
	echo '<input name="barbas_mail_sender_email_id" type="email" class="regular-text" value="' . get_option('barbas_mail_sender_email_id', '') . '" placeholder="no-reply@yourdomain.com"/>';
}

function barbas_mail_sender_text()
{
	// Display information about changing default mail sender name and email
	echo '<p>' . __('You may change your WordPress Default mail sender name and email.', 'barbas-dwms') . '</p>';
}

// Send test email
add_action('admin_init', 'barbas_mail_sender_send_test_email');
function barbas_mail_sender_send_test_email()
{
	if (isset($_POST['barbas_mail_sender_test_send'])) {
		$test_email = sanitize_email($_POST['barbas_mail_sender_test_email']);

		// Check if test email is not empty
		if (empty($test_email)) {
			// Display error for empty test email
			add_settings_error(
				'barbas_mail_sender_section',
				'barbas_mail_sender_test_email_empty',
				__('Please enter the email address for the test email.', 'barbas-dwms'),
				'error'
			);
			return;
		}

		// Check if email format is valid
		if (!is_email($test_email)) {
			// Display error for invalid email format
			add_settings_error(
				'barbas_mail_sender_section',
				'barbas_mail_sender_test_email_invalid',
				__('The email address you entered is not valid.', 'barbas-dwms'),
				'error'
			);
			return;
		}

		// Send the test email
		$subject = __('Test Email from Barbas Mail Sender', 'barbas-dwms');
		$message = __('This is a test email sent from Barbas Mail Sender plugin to verify the email sending functionality.', 'barbas-dwms');
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_option('barbas_mail_sender_id') . ' <' . get_option('barbas_mail_sender_email_id') . '>',
		);

		if (wp_mail($test_email, $subject, $message, $headers)) {
			// Display success message for test email sent
			add_settings_error(
				'barbas_mail_sender_section',
				'barbas_mail_sender_test_email_sent',
				__('Test email sent successfully!', 'barbas-dwms'),
				'updated'
			);
		} else {
			// Display error message for failed test email sending
			add_settings_error(
				'barbas_mail_sender_section',
				'barbas_mail_sender_test_email_failed',
				__('Failed to send test email. Please check your email settings.', 'barbas-dwms'),
				'error'
			);
		}
	}
}

// Create menu for sender settings
add_action('admin_menu', 'barbas_mail_sender_menu');
function barbas_mail_sender_menu()
{
	// Add menu page for sender settings
	add_menu_page(__('Settings', 'barbas-dwms'), __('Barbas Mail Sender', 'barbas-dwms'), 'manage_options', 'barbas_mail_sender', 'barbas_mail_sender_output', 'dashicons-email-alt2');
}

// Render sender settings page
function barbas_mail_sender_output()
{
?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<?php settings_errors(); ?>
		<form action="options.php" method="POST">
			<?php
			// Display sender settings fields
			do_settings_sections('barbas_mail_sender');
			settings_fields('barbas_mail_sender_section');
			submit_button();

			// Test Email Sender field
			?>
			<h2><?php _e('Test Mail Sender', 'barbas-dwms'); ?></h2>
			<p><?php _e('Enter the email address to send a test email:', 'barbas-dwms'); ?></p>
			<input name="barbas_mail_sender_test_email" type="email" class="regular-text" value="" placeholder="youremail@example.com" />
			<button class="button" type="submit" name="barbas_mail_sender_test_send"><?php _e('Send Test Email', 'barbas-dwms'); ?></button>
		</form>
	</div>
<?php
}

// Change the default WordPress email address
add_filter('wp_mail_from', 'barbas_new_mail_from');
function barbas_new_mail_from($old)
{
	if (!empty(get_option('barbas_mail_sender_email_id'))) {
		// Return the custom sender email if set
		return get_option('barbas_mail_sender_email_id');
	} else {
		// Trigger error if sender email is not set
		trigger_error(__('Barbas mail sender: The *sender email id* has not been set on', 'barbas-dwms') . get_bloginfo('url') . ".", E_USER_NOTICE);
		return $old;
	}
}

add_filter('wp_mail_from_name', 'barbas_new_mail_from_name');
function barbas_new_mail_from_name($old)
{
	if (!empty(get_option('barbas_mail_sender_id'))) {
		// Return the custom sender name if set
		return get_option('barbas_mail_sender_id');
	} else {
		// Trigger error if sender name is not set
		trigger_error(__('Barbas mail sender: The *sender id* has not been set on', 'barbas-dwms') . get_bloginfo('url') . ".", E_USER_NOTICE);
		return $old;
	}
}
