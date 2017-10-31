<?php

require_once OCRMF_INCLUDES_DIR . '/renderer.php';
require_once OCRMF_INCLUDES_DIR . '/mailer.php';
require_once OCRMF_INCLUDES_DIR . '/http.php';

class OneCRMForm {
	const POST_TYPE = 'onecrm_form';

	static protected $found_items = 0;
	static protected $current;

	static protected $stored_props = array(
		'fields',
		'mail_from',
		'mail_to',
		'mail_subject',
		'mail_body',
		'url',
		'onecrm',
		'lg',
		'success_url',
		'create_case',
		'method',
		'messages',
	);

	public $initial = false;
	public $id;
	public $name;
	public $title;
	public $form;
	public $fields;
	public $mail_from;
	public $mail_to;
	public $mail_subject;
	public $mail_body;
	public $url;
	public $success_url;
	public $method;
	public $onecrm;
	public $lg;
	public $validation_result = array();
	public $input = array();
	public $messages = array();

	private static $message_defaults;
	private static $message_descriptions;

	public function __construct( $post = null ) {

		if (empty(self::$message_defaults)) {
			self::$message_defaults = array(
				'required' => __('Please fill the required field.', OCRMF_TEXTDOMAIN),
				'invalid_number' => __('Number format seems invalid.', OCRMF_TEXTDOMAIN),
				'number_min' =>  __('Number is too small.', OCRMF_TEXTDOMAIN),
				'number_max' =>  __('Number is too large.', OCRMF_TEXTDOMAIN),
				'email' =>  __('Invalid email format.', OCRMF_TEXTDOMAIN),
				'url' =>  __('Invalid URL format.', OCRMF_TEXTDOMAIN),
				'phone' =>  __('Invalid telephone number format.', OCRMF_TEXTDOMAIN),
				'date' =>  __('Invalid date format.', OCRMF_TEXTDOMAIN),
				'error_posting' => __('Error posting form. Please try again later.', OCRMF_TEXTDOMAIN),
				'captcha' => __('You did not confirm that you are a human.', OCRMF_TEXTDOMAIN),
				'success' => __('Your message was sent. Thanks.', OCRMF_TEXTDOMAIN),
			);
			self::$message_descriptions = array(
				'required' => __('A required field was left empty.', OCRMF_TEXTDOMAIN),
				'invalid_number' => __('Number format is not invalid.', OCRMF_TEXTDOMAIN),
				'number_min' =>  __('Number is smaller than minimum limit.', OCRMF_TEXTDOMAIN),
				'number_max' =>  __('Number is larger than maximum limit.', OCRMF_TEXTDOMAIN),
				'email' =>  __('Email address is not invalid.', OCRMF_TEXTDOMAIN),
				'url' =>  __('URL is not invalid.', OCRMF_TEXTDOMAIN),
				'phone' =>  __('Telephone number is not valid.', OCRMF_TEXTDOMAIN),
				'date' =>  __('Date entry is not valid.', OCRMF_TEXTDOMAIN),
				'error_posting' => __('An error occured when posting the form.', OCRMF_TEXTDOMAIN),
				'captcha' => __('Visitor failed to solve CAPTCHA.', OCRMF_TEXTDOMAIN),
				'success' => __('Messages sent successfully.', OCRMF_TEXTDOMAIN),
			);
		}
		$this->initial = true;
		$this->form = '';
        $this->url = get_option('ocrmf_default_url');
		$post = get_post( $post );

		if ( $post && self::POST_TYPE == get_post_type($post) ) {
			$this->initial = false;
			$this->id = $post->ID;
			$this->name = $post->post_name;
			$this->title = $post->post_title;
			$this->form = $post->post_content;

			foreach (self::$stored_props as $k) {
				$this->$k = get_post_meta($post->ID, 'ocrmf_' . $k, true);
			}
		}
	}

	public function save() {
		echo '<pre>';
		if ( $this->initial ) {
			$post_id = wp_insert_post( array(
				'post_type' => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim($this->form)));
		} else {
			$post_id = wp_update_post( array(
				'ID' => (int) $this->id,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim($this->form)));
		}
		if ($post_id) {
			foreach (self::$stored_props as $k) {
				update_post_meta($post_id, 'ocrmf_' . $k, $this->$k);
			}
		}
		return $post_id;
	}
	
	public function set_validation_result($result) {
		$this->validation_result = $result;
	}

	public function set_input($input) {
		$this->input = $input;
	}

	public function copy() {
		$new = new self;
		$new->initial = true;
		$new->title = $this->title . '_copy';
		$new->form = $this->form;
		$new = apply_filters_ref_array( 'ocrmf_copy', array( &$new, &$this ) );
		return $new;
	}

	public function delete() {
		if ( $this->initial )
			return;

		if ( wp_delete_post( $this->id, true ) ) {
			$this->initial = true;
			$this->id = null;
			return true;
		}

		return false;
	}

	public static function set_current(self $obj) {
		self::$current = $obj;
	}

	public static function get_current() {
		return self::$current;
	}

	public static function register_post_types() {
		register_post_type(
			self::POST_TYPE, 
			array(
				'labels' => array(
					'name' => __( 'Forms', OCRMF_TEXTDOMAIN ),
					'singular_name' => __( 'Form', OCRMF_TEXTDOMAIN),
				),
				'rewrite' => false,
				'query_var' => false,
			)
		);
		add_shortcode( 'onecrm-form', array('OneCRMFormRenderer', 'shortcode_func' ));
	}

	public static function count() {
		return self::$found_items;
	}

	public static function find($args = '') {
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC' );

		$args = wp_parse_args($args, $defaults);

		$args['post_type'] = self::POST_TYPE;

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post )
			$objs[] = new self( $post );

		return $objs;
	}

	public function submit($data, &$response) {
		$mailer = new OneCRMFormMailer($this, $data);
		$result = $mailer->mail();
		$response['mail'] = $result;
		$http = new OneCRMFormHTTP($this, $data);
		$http->send($response);
		if (strlen($this->success_url))
			$response['redirect'] = $this->success_url;
		$response['xxx'] = $_POST;
	}

	public function add_js_messages() {
		$messages = $this->get_messages();
		wp_localize_script('ocrmf-forms', '_ocrmf_messages_' . $this->id, $messages);
	}

	public function get_messages($add_descriptions = false) {
		$messages = self::$message_defaults;
		$descriptions = self::$message_descriptions;
		foreach ($messages as $k => &$msg) {
			if (isset($this->messages[$k]))
				$msg = $this->messages[$k];
			if ($add_descriptions)
				$msg = array($msg, $descriptions[$k]);
		}
		return $messages;
	}

}

