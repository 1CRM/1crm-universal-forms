<?php

require_once OCRMF_INCLUDES_DIR . '/form.php';
require_once OCRMF_INCLUDES_DIR . '/gcaptcha/GCaptchaAPIClient.php';

class OneCRMFormValidator {
	private $form;

	private static $validators = array(
		'text' => array(
			array('required'),
		),
		'spinbox' => array(
			array('required'),
			array('range'),
		),
		'slider' => array(
			array('required'),
			array('range'),
		),
		'date' => array(
			array('required'),
			array('re', '~[0-9]{4}-[0-9]{2}-[0-9]{2}~', 'date'),
		),
		'textarea' => array(
			array('required'),
		),
		'select' => array(
			array('required'),
		),
		'radio' => array(
			array('required_always'),
		),
		'checkbox' => array(
			array('required'),
		),
		'email' => array(
			array('required'),
			array('email'),
		),
		'url' => array(
			array('required'),
			array('url'),
		),
		'phone' => array(
			array('required'),
			array('re', '~^[+]?[0-9() -]*$~', 'phone'),
		),
		'captcha' => array(
			array('captcha')
		),
	);

	public function __construct(OneCRMForm $form){
		$this->form = $form;
	}

	private function get_posted_value($input, $name) {
		return isset($input[$name]) ? $input[$name] : null;
	}

	private function is_valid_empty($field, $value) {
		return empty($field->required) && !strlen($value);
	}

	public function validate($input) {
		$result = array(
			'valid' => true,
		);

		$fields = json_decode($this->form->fields);
		foreach ($fields as $field) {
			$value = $this->get_posted_value($input, $field->name);
			$validators = array();
			if (isset(self::$validators[$field->type]))
				$validators = self::$validators[$field->type];
			if (!empty($field->validator)) {
				$msg = empty($field->validator_message) ? 'Invalid Value' : $field->validator_message;
				$validators[] = array('re', '~' . $field->validator . '~', $msg);
			}
			if (empty($validators))
				continue;
			foreach ($validators as $v) {
				$method = 'validate_' . $v[0];
				$valid = $this->$method($field, $value, $v, $result);
				if (!$valid) {
					$result['valid'] = false;
					break;
				}
			}
		}
		
		return $result;
	}

	public function validate_required($field, $value, $validator, &$result) {
		if (!empty($field->required)) {
			$name = $field->name;
			if (!empty($field->multiple))
				$name .= '[]';
			if (!empty($field->multiple) ? empty($value): !strlen($value)) {
				$result['errors'][$name] = 'required';
				return false;
			}
		}
		return true;
	}

	public function validate_required_always($field, $value, $validator, &$result) {
		if (!strlen($value)) {
			$result['errors'][$field->name] = 'required';
			return false;
		}
		return true;
	}

	public function validate_range($field, $value, $validator, &$result) {
		if ($this->is_valid_empty($field, $value))
			return true;
		if (!is_numeric($value)) {
			$result['errors'][$field->name] = 'invalid_number';
			return false;
		}
		if (isset($field->min) && strlen($field->min)) {
			$min = (double)$field->min;
			if ($min > $value) {
				$result['errors'][$field->name] = 'number_min';
				return false;
			}
		}
		if (isset($field->max) && strlen($field->max)) {
			$max = (double)$field->max;
			if ($max < $value) {
				$result['errors'][$field->name] = 'number_max';
				return false;
			}
		}
		return true;
	}

	public function validate_re($field, $value, $validator, &$result) {
		if ($this->is_valid_empty($field, $value))
			return true;
		$re = $validator[1];
		if (!preg_match($re, $value)) {
			$result['errors'][$field->name] = $validator[2];
			return false;
		}
		return true;
	}
	
	public function validate_email($field, $value, $validator, &$result) {
		if ($this->is_valid_empty($field, $value))
			return true;
		if (!is_email($value)) {
			$result['errors'][$field->name] = 'email';
			return false;
		}
		return true;
	}

	public function validate_url($field, $value, $validator, &$result) {
		if ($this->is_valid_empty($field, $value))
			return true;
		if (filter_var($value, FILTER_VALIDATE_URL) === false) {
			$result['errors'][$field->name] = 'url';
			return false;
		}
		return true;
	}

	public function validate_captcha($field, $value, $validator, &$result) {
		$secret = get_option('ocrmf_recaptcha_secret');
		$validator = new GCaptchaAPIClient($secret);
		$data = $validator->validate($value);
		$name = $field->name;
		if (!$data || !$data['success']) {
			$result['errors'][$name] = 'captcha';
			return false;
		}
		return true;

	}

}
