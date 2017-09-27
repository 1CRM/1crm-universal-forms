<?php

add_action( 'admin_menu', 'ocrmf_admin_menu', 9 );
require_once OCRMF_ADMIN_DIR . '/admin-functions.php';

function ocrmf_admin_menu() {
	$icon_url = '';

	add_object_page(
		__('1CRM Form', OCRMF_TEXTDOMAIN),
		__('1CRM Universal Forms', OCRMF_TEXTDOMAIN),
		'ocrmf_read',
		'ocrmf',
		'ocrmf_admin_page',
		$icon_url
	);

	$edit = add_submenu_page(
		'ocrmf',
		__('Edit Form', OCRMF_TEXTDOMAIN),
		__('Forms', OCRMF_TEXTDOMAIN),
		'ocrmf_read', 'ocrmf',
		'ocrmf_admin_page' );

	add_action( 'load-' . $edit, 'ocrmf_admin_load' );

	$addnew = add_submenu_page(
		'ocrmf',
		__('Add New Form', OCRMF_TEXTDOMAIN),
		__('Add New', OCRMF_TEXTDOMAIN),
		'ocrmf_edit',
		'ocrmf-new',
		'ocrmf_admin_edit' );

	add_action( 'load-' . $addnew, 'ocrmf_admin_load' );
}

function ocrmf_admin_page() {
	if ( $post = ocrmf_get_current_form() ) {
		$post_id = $post->initial ? -1 : $post->id;
		include OCRMF_ADMIN_DIR . '/tpl-edit-form.php';
		return;
	}
	require_once OCRMF_ADMIN_DIR . '/list-table.php';
	include OCRMF_ADMIN_DIR . '/tpl-list-forms.php';
}

function ocrmf_admin_edit() {
	if ( $post = ocrmf_get_current_form() ) {
		$post_id = -1;

		include OCRMF_ADMIN_DIR . '/tpl-edit-form.php';
		return;
	}
}

function ocrmf_admin_load() {
	global $plugin_page;

	$action = ocrmf_current_action();

	if ( 'save' == $action ) {
		$id = $_POST['post_ID'];
		check_admin_referer( 'ocrmf-save-contact-form_' . $id );

		if (!current_user_can( 'ocrmf_edit', $id))
			wp_die(__('You are not allowed to edit this item.', OCRMF_TEXTDOMAIN));

		if ( !($contact_form = ocrmf_form($id)) ) {
			$contact_form = new OneCRMForm();
			$contact_form->initial = true;
		}

		$contact_form->title = trim( $_POST['ocrmf-title'] );

		echo '<pre>';
		$form = trim($_POST['ocrmf-form']);
		$fields = $_POST['ocrmf-fields'];
		$mail_from = $_POST['ocrmf-from'];
		$mail_to = $_POST['ocrmf-to'];
		$mail_subject = $_POST['ocrmf-subject'];
		$mail_body = $_POST['ocrmf-body'];
		$url = $_POST['ocrmf-url'];
		$success_url = $_POST['ocrmf-success-url'];
		$create_case = $_POST['ocrmf-create-case'];
		$method = $_POST['ocrmf-method'];
		$onecrm = $_POST['ocrmf-onecrm'];
		$lg = $_POST['ocrmf-lg'];
		$messages = $_POST['ocrmf-msg'];

		$props = apply_filters(
			'ocrmf_admin_posted_properties',
			compact('form', 'fields', 'mail_from', 'mail_to', 'mail_subject', 'mail_body', 'url', 'method', 'messages', 'success_url', 'onecrm', 'lg', 'create_case')
		);

		foreach ( (array) $props as $key => $prop )
			$contact_form->{$key} = $prop;

		$query = array();
		$query['message'] = ($contact_form->initial) ? 'created' : 'saved';

		$contact_form->save();

		$query['post'] = $contact_form->id;

		$redirect_to = add_query_arg( $query, menu_page_url( 'ocrmf', false ) );

		wp_safe_redirect( $redirect_to );
		exit();
	}

	if ( 'copy' == $action ) {
		$id = empty( $_POST['post_ID'] ) ? absint($_REQUEST['post']) : absint($_POST['post_ID']);

		check_admin_referer( 'ocrmf-copy-contact-form_' . $id );

		if (!current_user_can( 'ocrmf_edit', $id))
			wp_die(__('You are not allowed to edit this item.', OCRMF_TEXTDOMAIN));

		$query = array();

		if ( $contact_form = ocrmf_form($id)) {
			$new_contact_form = $contact_form->copy();
			$new_contact_form->save();

			$query['post'] = $new_contact_form->id;
			$query['message'] = 'created';
		}

		$redirect_to = add_query_arg($query, menu_page_url('ocrmf', false));

		wp_safe_redirect($redirect_to);
		exit();
	}

	if ( 'delete' == $action ) {
		if ( ! empty( $_POST['post_ID'] ) )
			check_admin_referer( 'ocrmf-delete-contact-form_' . $_POST['post_ID'] );
		elseif ( ! is_array( $_REQUEST['post'] ) )
			check_admin_referer( 'ocrmf-delete-contact-form_' . $_REQUEST['post'] );
		else
			check_admin_referer( 'bulk-posts' );

		$posts = empty($_POST['post_ID']) ? (array)$_REQUEST['post'] : (array) $_POST['post_ID'];

		$deleted = 0;

		foreach ( $posts as $post ) {
			$post = new OneCRMForm($post);

			if ( empty( $post ) )
				continue;

			if ( ! current_user_can( 'ocrmf_edit', $post->id ) )
				wp_die(__('You are not allowed to delete this item.', OCRMF_TEXTDOMAIN));

			if (!$post->delete())
				wp_die(__( 'Error in deleting.', OCRMF_TEXTDOMAIN));

			$deleted += 1;
		}

		$query = array();

		if (!empty($deleted))
			$query['message'] = 'deleted';

		$redirect_to = add_query_arg($query, menu_page_url('ocrmf', false));

		wp_safe_redirect( $redirect_to );
		exit();
	}

	$_GET['post'] = isset($_GET['post']) ? $_GET['post'] : '';

	$post = null;

	if ('ocrmf-new' == $plugin_page) {
		$post = new OneCRMForm;
		$post->initial = true;
	} elseif (!empty($_GET['post'])) {
		$post = ocrmf_form($_GET['post']);
	}

	if ( $post && current_user_can( 'ocrmf_edit', $post->id ) ) {
		ocrmf_add_meta_boxes($post->id);
		include OCRMF_ADMIN_DIR . '/help.php';
	} else {
		$current_screen = get_current_screen();

		require_once OCRMF_ADMIN_DIR . '/list-table.php';

		add_filter( 'manage_' . $current_screen->id . '_columns',
			array( 'OneCRM_List_Table', 'define_columns' ) );

		add_screen_option(
			'per_page',
			array(
				'label' => __('Forms', OCRMF_TEXTDOMAIN),
				'default' => 20,
				'option' => 'ocrmf_per_page'
			)
		);
	}

	if ( $post ) {
		OneCRMForm::set_current( $post );
	}
}

add_action( 'admin_enqueue_scripts', 'ocrmf_admin_enqueue_scripts' );

function ocrmf_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'ocrmf' ) )
		return;

	wp_enqueue_style(
		'ocrmf-admin',
		OCRMF_PLUGIN_URL .  '/admin/css/styles.css',
		array(), OCRMF_VERSION,
		'all'
	);

	wp_enqueue_script(
		'ocrmf-editor',
		OCRMF_PLUGIN_URL . '/admin/js/form_editor.js',
		array( 'jquery', 'postbox' ),
		OCRMF_VERSION,
		true
	);

	$current_screen = get_current_screen();

	wp_localize_script('ocrmf-editor', '_ocrmf', array(
		'screenId' => $current_screen->id,
	));
}

