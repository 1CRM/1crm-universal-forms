<?php

get_current_screen()->add_help_tab(
	array(
		'id'      => 'fields',
		'title'   => __('Creating fields', OCRMF_TEXTDOMAIN),
		'content' => '<p>' . __('In the <strong>Fields</strong> box, you can add fields to be used in the form. Click the <strong>Add Field</strong> button, and select field type. For each field you specify field name, and a number of options. Most options are self-explanatory. For <strong>Drop-down menu</strong> and <strong>Radio buttons</strong> inputs, you have to specify a list of choices. Each choice is placed on a separate line in corresponding tex box. You can use <strong>|</strong> (pipe symbol) specify both posted value and displayed value for a choice. For example, <strong>option1|Option Value 1</strong> displays <strong>Option Value 1</strong> to user, but external script will receive <strong>option1</strong>. If you do not use the pipe symbol, both posted value and displayed value will be the same.', OCRMF_TEXTDOMAIN)  . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'form',
		'title'   => __('Inserting fields', OCRMF_TEXTDOMAIN),
		'content' => 
			'<p>'
			. __('The content of the form must be entered into <strong>Form content</strong> box. You can use any HTML markup you wish, it will be copied to post, page or widget using [onecrm-form] shortcode. To insert form fields, enter field names in curly brackets. For example, <strong>{emai}</strong> will be replaced with HTML input for <strong>email</strong> field.', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('You can use <strong>A</strong> button next to field name to insert it into <strong>Form content</strong> box in current cursor position.', OCRMF_TEXTDOMAIN)
			. '</p>'
			,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'email',
		'title'   => __('Sending email', OCRMF_TEXTDOMAIN),
		'content' => 
			'<p>'
			. __('This plugin can send an email with values the user entered into form. To have an email sent, you specify sender, recipient, subject and email body in corresponding fields, optionally using field  value placeholders, in the same manner as in the form content.', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('Sending email is optional. If you leave <strong>To</strong> field blank, no email will be sent.', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('Multiple recipients can be specified in <strong>To</strong> field, separated by comma. Each recipient can be either an email address or a combination of name and address, in the following format: <br /><strong>Jonh Smith &lt;john.smith@example.com&gt;</strong>.', OCRMF_TEXTDOMAIN)
			. '</p>'
			,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => '1crm',
		'title'   => __('Sending data to 1CRM', OCRMF_TEXTDOMAIN),
		'content' =>
			'<p>'
			. __('Optionally, the form data can be submitted to 1CRM, to create or update a lead', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('To enable submitting form data to 1CRM, set <b>Send lead data to 1CRM</b> in form options 
			to either <b>Create</b> or <b>Create or Update</b>, and make sure you define 1CRM field mappings.', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('If you select "Create", each form submission will result in new lead created in 1CRM. ', OCRMF_TEXTDOMAIN)
			. __('If you select "Create or Update", email field will be used to find an existing lead in 1CRM. ', OCRMF_TEXTDOMAIN)
		    . __('If existing lead was found, it will be updated with data from the form, otherwise a new lead will be created', OCRMF_TEXTDOMAIN)
			. '</p>'
			,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'lg',
		'title'   => __('Sending data to Lead Guerrilla', OCRMF_TEXTDOMAIN),
		'content' =>
			'<p>'
			. __('Optionally, the form data can be submitted to Lead Guerrilla, to create or update a lead', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('To enable submitting form data to Lead Guerrilla, set <b>Send lead data to 1CRM</b> in form options 
			to either <b>Create</b> or <b>Create or Update</b>, and make sure you define 1CRM field mappings.', OCRMF_TEXTDOMAIN)
			. '</p>'
			. '<p>'
			. __('If you select "Create", each form submission will result in new lead created in Lead Guerrilla. ', OCRMF_TEXTDOMAIN)
			. __('If you select "Create or Update", email field will be used to find an existing lead in Guerrilla. ', OCRMF_TEXTDOMAIN)
		    . __('If existing lead was found, it will be updated with data from the form, otherwise a new lead will be created', OCRMF_TEXTDOMAIN)
			. '</p>'
			,
	)
);


