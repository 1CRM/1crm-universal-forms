<?php


require_once OCRMF_INCLUDES_DIR . '/validator.php';
add_action( 'init', 'ocrmf_controller_init', 12);

wp_enqueue_style(
	'ocrmf-style',
	OCRMF_PLUGIN_URL .  '/includes/css/styles.css',
	array(), OCRMF_VERSION,
	'all'
);


function ocrmf_controller_init()
{
	ocrmf_controller_json();
	ocrmf_controller_submit();
}

function ocrmf_controller_json() {
	if (empty($_POST['_ocrmf_ajax']))
		return;
	if (isset($_POST['_ocrmf_id'])) {
		if ($form = ocrmf_form((int)$_POST['_ocrmf_id'])) {
			$validator = new OneCRMFormValidator($form);
			$response = $validator->validate($_POST);
			if ($response['valid'])
				$form->submit($_POST, $response);
			@header('Content-Type: application/json; charset=' . get_option('blog_charset'));
			echo json_encode($response);
			exit;
		}
	}
}

function ocrmf_controller_submit() {
	if (empty($_POST['_ocrmf_id']) || !empty($_POST['_ocrmf_ajax']))
		return;
	
	if ($form = ocrmf_form((int)$_POST['_ocrmf_id'])) {
		$validator = new OneCRMFormValidator($form);
		$input = $_POST;
		$response = $validator->validate($input);
		$response['posted'] = true;
		if ($response['valid'])
			$form->submit($_POST, $response);
		$form->set_validation_result($response);
		$form->set_input($input);
		OneCRMForm::set_current($form);
	}
}

