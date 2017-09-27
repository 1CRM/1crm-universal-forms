<?php

require_once OCRMF_INCLUDES_DIR . '/mautic_api/autoload.php';  
use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

function ocrmf_current_action() {
	if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
		return $_REQUEST['action'];

	if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
		return $_REQUEST['action2'];

	return false;
}

function ocrmf_get_current_form() {
	if ( $current = OneCRMForm::get_current() ) {
		return $current;
	}
}


add_action( 'admin_menu', 'ocrmf_plugin_menu' );
add_action( 'admin_init', 'register_ocrmf_settings' );

function register_ocrmf_settings() {
	register_setting( 'ocrmf_options_group', 'ocrmf_onecrm_url' ); 
	register_setting( 'ocrmf_options_group', 'ocrmf_onecrm_id' ); 
	register_setting( 'ocrmf_options_group', 'ocrmf_onecrm_secret' ); 
	register_setting( 'ocrmf_options_group', 'ocrmf_lg_url' ); 
	register_setting( 'ocrmf_options_group', 'ocrmf_lg_id' ); 
	register_setting( 'ocrmf_options_group', 'ocrmf_lg_secret' ); 
} 


function ocrmf_plugin_menu() {
	add_options_page( '1CRM Forms Options', '1CRM Universal Forms', 'manage_options', 'ocrmf_options', 'ocrmf_plugin_options' );
}



function ocrmf_validate_lg_token() {
	$auth_redirect =  admin_url('/options-general.php?page=ocrmf_options', false );
	$baseUrl = get_option('ocrmf_lg_url');
    $clientKey = get_option('ocrmf_lg_id');
	$clientSecret = get_option('ocrmf_lg_secret');
	$settings = array(
	    'baseUrl'          => $baseUrl,
	    'version'          => 'OAuth2',
	    'clientKey'        => $clientKey,
		'clientSecret'     => $clientSecret,
		'callback'         =>  $auth_redirect,
	);

	$initAuth = new ApiAuth();
	$auth = $initAuth->newAuth($settings);

	try {
		if ($auth->validateAccessToken()) {
			$accessTokenData = $auth->getAccessTokenData();
			update_option('ocrmf_lg_token', $accessTokenData);
			wp_safe_redirect( $auth_redirect );
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function ocrmf_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$auth_redirect =  admin_url('/options-general.php?page=ocrmf_options', false );
	echo '<div class="wrap">';
?>
<?php 
	if (!empty($_GET['code']))
		ocrmf_validate_lg_token() ;
?>
<h2>1CRM Forms Options</h2>
<br/>
<div style="font-size: 110%; max-width: 600px">
To connect Wordpress with 1CRM, first create an API client in 1CRM. Enabled Grant Types must be  "Client Credentials".
Then enter 1CRM URL, Client ID and Client Secret below
</div>
<form method="post" action="options.php"> 
<?php settings_fields( 'ocrmf_options_group' );
do_settings_sections('ocrmf_options_group');
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">1CRM URL</th>
        <td><input type="text" name="ocrmf_onecrm_url" value="<?php echo get_option('ocrmf_onecrm_url'); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">1CRM API ID</th>
        <td><input type="text" name="ocrmf_onecrm_id" value="<?php echo get_option('ocrmf_onecrm_id'); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">1CRM API Secret</th>
        <td><input type="text" name="ocrmf_onecrm_secret" value="<?php echo get_option('ocrmf_onecrm_secret'); ?>" /></td>
	</tr>
</table>
<div style="font-size: 110%; max-width: 600px">
To connect Wordpress with Lead Guerrilla, first create API Credentials in Lead Guerrilla, making sure that Authorization Protocol
is OAuth 2. Then enter Lead Guerrilla URL, Client ID and Client Secret below. <b>After</b> saving the settings, click
"Authorize at Lead Guerrilla"
</div>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Lead Guerrilla URL</th>
        <td><input type="text" name="ocrmf_lg_url" value="<?php echo get_option('ocrmf_lg_url'); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Lead Guerrilla API ID</th>
        <td><input type="text" name="ocrmf_lg_id" value="<?php echo get_option('ocrmf_lg_id'); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Lead Guerrilla API Secret</th>
        <td><input type="text" name="ocrmf_lg_secret" value="<?php echo get_option('ocrmf_lg_secret'); ?>" /></td>
	</tr>
</table>
<?php submit_button(); ?>
</form>
<?php

$token = get_option('ocrmf_lg_token');
if ($token) {
	$formatstr_good = esc_html(__('Lead Guerrilla access token will expire on %s', OCRMF_TEXTDOMAIN));
	$formatstr_bad = esc_html(__('Lead Guerrilla access token expired on %s', OCRMF_TEXTDOMAIN));
	$date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $token['expires'] + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
	$valid_token = $token['expires'] > time();
	$expiration_info = sprintf($valid_token ? $formatstr_good : $formatstr_bad, $date);
}
?>
<?php if ($token) : ?>
	<div style="font-weight: bold;font-size:120%;color:<?php echo $valid_token ? 'green' : 'red'?>">
	<?php echo $expiration_info?>
	</div>
<?php else : ?>
	<div style="font-weight: bold;font-size:120%;color:red">
		<?php echo esc_html(__('Wordpress is not authorized at Lead Guerrilla', OCRMF_TEXTDOMAIN));?>
	</div>
<?php endif?>
<br>
<a href="<?php echo get_option('ocrmf_lg_url')?>/oauth/v2/authorize?client_id=<?php echo get_option('ocrmf_lg_id')?>&grant_type=authorization_code&redirect_uri=<?php echo urlencode($auth_redirect)?>&response_type=code">Authorize at Lead Guerrilla</a>

<?php
	echo '</div>';
}



function ocrmf_add_meta_boxes( $post_id ) {
	add_meta_box( 'fieldsdiv', __( 'Fields', OCRMF_TEXTDOMAIN),
		'ocrmf_fields_meta_box', null, 'fields', 'core');
	add_meta_box( 'buttonsdiv', __( 'Actions', OCRMF_TEXTDOMAIN),
		'ocrmf_buttons_meta_box', null, 'buttons', 'core');
	add_meta_box( 'emaildiv', __( 'Email', OCRMF_TEXTDOMAIN),
		'ocrmf_email_meta_box', null, 'email', 'core');
	add_meta_box( 'scriptdiv', __( 'Options', OCRMF_TEXTDOMAIN),
		'ocrmf_script_meta_box', null, 'script', 'core');
	add_meta_box( 'msgdiv', __( 'Messages', OCRMF_TEXTDOMAIN),
		'ocrmf_msg_meta_box', null, 'msg', 'core');
	add_meta_box( 'importdiv', __( 'Import', OCRMF_TEXTDOMAIN),
		'ocrmf_import_meta_box', null, 'import', 'core');

	add_filter( 'postbox_classes_toplevel_page_ocrmf_msgdiv', 'ocrmf_close_metabox');
	add_filter( 'postbox_classes_toplevel_page_ocrmf_scriptdiv', 'ocrmf_close_metabox');
	add_filter( 'postbox_classes_toplevel_page_ocrmf_emaildiv', 'ocrmf_close_metabox');
	add_filter( 'postbox_classes_toplevel_page_ocrmf_importdiv', 'ocrmf_close_metabox');
}

function ocrmf_close_metabox($classes)
{
	$classes[] = 'closed';
	return $classes;
}

function ocrmf_fields_meta_box($post) {
	$types = array(
		'text' => 'Text field',
		'email' => 'Email',
		'url' => 'URL',
		'phone' => 'Telephone number',
		'spinbox' => 'Number (spinbox)',
		'slider' => 'Number (slider)',
		'date' => 'Date',
		'textarea' => 'Text area',
		'select' => 'Drop-down menu',
		'checkbox' => 'Checkbox',
		'radio' => 'Radio buttons',
		'hidden' => 'Hidden field',
		'submit' => 'Submit button',
	);
?>
	<input onclick="jQuery('#field_types_list').toggle();" type="button" class="button" value="<?php echo esc_html(__('Add Field', OCRMF_TEXTDOMAIN)) ?>">
	<div id="field_types_list" class="field-list-dropdown">
		<?php foreach ($types as $type=> $label) : ?>
		<div data-id="<?php echo $type?>"><?php echo esc_html(__($label)) ?></div>
		<?php endforeach ?>
	</div>
	<div id="fields-container">
	</div>

<?php
}


function ocrmf_buttons_meta_box($post) { ?>
	<?php if (current_user_can( 'ocrmf_edit', $post->id)) : ?>
		<input type="submit" onclick="return OneCRMFormEditor.validate()" class="button-primary" name="ocrmf-save" value="<?php echo esc_attr( __( 'Save', OCRMF_TEXTDOMAIN)); ?>" />
	<?php endif; ?>

	<?php if ( current_user_can( 'ocrmf_edit', $post->id ) && ! $post->initial ) : ?>
		<?php $copy_nonce = wp_create_nonce( 'ocrmf-copy-contact-form_' . $post->id ); ?>
		<input type="submit" name="ocrmf-copy" class="button" value="<?php echo esc_attr( __( 'Duplicate', OCRMF_TEXTDOMAIN)); ?>"
		<?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
		<?php $delete_nonce = wp_create_nonce( 'ocrmf-delete-contact-form_' . $post->id ); ?>
		<input type="submit" name="ocrmf-delete" class="button" value="<?php echo esc_attr( __( 'Delete', OCRMF_TEXTDOMAIN)); ?>"
		<?php echo "onclick=\"if (confirm('" .
			esc_js(__( "You are about to delete this contact form.\n  'Cancel' to stop, 'OK' to delete.", OCRMF_TEXTDOMAIN)) .
			"')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
	<?php endif; ?>
<?php
}

function ocrmf_email_meta_box($post) { ?>
<div>
	<div>
		<span class="ocrmf-label">To</span>
		<span class="ocrmf-field"><input type="text" name="ocrmf-to" value="<?php echo esc_attr($post->mail_to)?>" /></span>
	</div>
	<div>
		<span class="ocrmf-label">From</span>
		<span class="ocrmf-field"> <input type="text" name="ocrmf-from" value="<?php echo esc_attr($post->mail_from)?>" /></span>
	</div>
	<div>
		<span class="ocrmf-label">Subject</span>
		<span class="ocrmf-field"> <input type="text" name="ocrmf-subject" value="<?php echo esc_attr($post->mail_subject)?>" /></span>
	</div>
	<br />
	Text:<br />
	<textarea style="width: 100%; height: 200px" name="ocrmf-body" id="ocrmf-body"><?php echo esc_html($post->mail_body)?></textarea>
</div>
<?php
}

function ocrmf_script_meta_box($post) {
?>
<div>
	<div>
		<span class="ocrmf-label"><?php echo esc_html(__('Send lead data to 1CRM', OCRMF_TEXTDOMAIN))?></span>
		<span class="ocrmf-field"><select name="ocrmf-onecrm">
			<option value=""><?php echo esc_html(__('Disabled', OCRMF_TEXTDOMAIN))?></option>
			<option value="create" <?php if ($post->onecrm == 'create') echo ' selected="selected"' ?>>
				<?php echo esc_html(__('Create', OCRMF_TEXTDOMAIN))?>
			</option>
			<option value="update" <?php if ($post->onecrm == 'update') echo ' selected="selected"' ?>>
				<?php echo esc_html(__('Create or update', OCRMF_TEXTDOMAIN))?>
			</option>
		</select></span>
	</div>
	<div>
		<span class="ocrmf-label"><label for="ocrmf-create-case"><?php echo esc_html(__('Create a case in 1CRM', OCRMF_TEXTDOMAIN))?></label></span>
		<span class="ocrmf-field"><input type="checkbox" name="ocrmf-create-case" id="ocrmf-create-case" value="1" <?php if ($post->create_case) echo ' checked="checked"';?>  ></span>
	</div>
	<div>
		<span class="ocrmf-label"><?php echo esc_html(__('Send lead data to Lead Guerrilla', OCRMF_TEXTDOMAIN))?></span>
		<span class="ocrmf-field"><select name="ocrmf-lg">
			<option value=""><?php echo esc_html(__('Disabled', OCRMF_TEXTDOMAIN))?></option>
			<option value="create" <?php if ($post->lg == 'create') echo ' selected="selected"' ?>>
				<?php echo esc_html(__('Create', OCRMF_TEXTDOMAIN))?>
			</option>
			<option value="update" <?php if ($post->lg == 'update') echo ' selected="selected"' ?>>
				<?php echo esc_html(__('Create or update', OCRMF_TEXTDOMAIN))?>
			</option>
		</select></span>
	</div>
	<div>
		<span class="ocrmf-label"><?php echo esc_html(__('Redirect URL', OCRMF_TEXTDOMAIN))?></span>
		<span class="ocrmf-field"><input type="text" name="ocrmf-success-url" value="<?php echo esc_html($post->success_url) ?>"></span>
	</div>
</div>
<?php
}

function ocrmf_msg_meta_box($post) {
	$messages = $post->get_messages(true);
	foreach ($messages as $k => $msg) { ?>
		<div style="font-style:italic"><?php echo esc_html($msg[1]) ?></div>
		<input type="text" style="width:100%" name="ocrmf-msg[<?php echo $k?>]" value="<?php echo esc_html($msg[0]) ?>" />
<?php
	}
}

function ocrmf_import_meta_box($post) { ?>
<textarea style="width:100%" rows="8" id="ocrmf-import-box"></textarea>
<input type="button" onclick="OneCRMFormEditor.import(); return false;" class="button" name="ocrmf-import" value="<?php echo esc_attr( __( 'Import', OCRMF_TEXTDOMAIN)); ?>" />

<?php
}
