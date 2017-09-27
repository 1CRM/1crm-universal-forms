<?php

require_once OCRMF_INCLUDES_DIR . '/form.php';
require_once OCRMF_INCLUDES_DIR . '/mautic_api/autoload.php';  
require_once OCRMF_INCLUDES_DIR . '/onecrm_api/OneCRMApiClient.php';  

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

class OneCRMFormHTTP {

	private $form;
	private $fields;
	private $input;

	public function __construct(OneCRMForm $form, $input) {
		$this->form = $form;
		$this->input = $input;
		$this->fields = json_decode($this->form->fields);
	}

	public function send(&$response) {
	
		if ($this->form->lg) {
			$mapped = $this->mapFields('lg_field');
			$this->sendToLG($mapped, $this->form->lg);
		}
		if ($this->form->onecrm) {
			$mapped = $this->mapFields('onecrm_field');
			$this->sendToOneCRM($mapped, $this->form->onecrm);
		}
		if ($this->form->create_case) {
			$mapped = $this->mapFields('onecrm_case_field');
			$this->createCase($mapped);
		}

		$json = @json_decode($return);
		$response['http_result'] = $json;
	}

	private function mapFields($key) {
		$mapped = array();
		$fields = json_decode($this->form->fields, true);
		foreach ($fields as $f) {
			if (isset($f[$key])) {
				$real_key = $key;
				if ($f[$key] == '_') {
					$real_key = $key . '_custom';
				}
				if (isset($this->input[$f['name']])) {
					$rk = $f[$real_key];
					$fname = $f['name'];
					if (!array_key_exists($rk, $mapped)) {
						$mapped[$rk] = $this->input[$fname];
					} else {
						$mapped[$rk] .= "\n\n";
						$mapped[$rk] .= $fname . ': ';
						$mapped[$rk] .= $this->input[$fname];
					}
				}
			}
		}
		return $mapped;
	}

	private function sendToOneCRM($data, $mode) {
		$client = new OneCRMApiClient;
		if ($mode == 'create' || (empty($data['email1']) && empty($data['email2']))) {
			$result = $client->sendRequest('/data/Lead', 'POST', array(), array('data' => $data));
			if ($result === false)
				return;
		} else {
			$query = array('limit' => 1, 'filters' => array());
			if (!empty($data['email1']))
				$query['filters']['email'] = $data['email1'];

			$result = $client->sendRequest('/data/Lead', 'GET', $query);
			if ($result === false)
				return;
			if (isset($result['message'])) {
				return;
			}
			if (isset($result['records'][0])) {
				$result = $client->sendRequest('/data/Lead/' . $result['records'][0]['id'], 'PATCH', array(), array('data' => $data));
			} else {
				$result = $client->sendRequest('/data/Lead', 'POST', array(), array('data' => $data));
			}
		}
		
	}

	private function createCase($data) {
		$client = new OneCRMApiClient;
		$result = $client->sendRequest('/data/aCase', 'POST', array(), array('data' => $data));
	}

	private function sendToLG($data, $mode) {
		$api = $this->authenticateLG();
		if (!$api)
			return;
		try {
			if ($mode == 'create' || empty($data['email'])) {
				$response = $api->create($data);
			} else {
    			$list = $api->getList('email:' . $data['email'], 0, 1);
				if (empty($list['contacts'])) {
					$response = $api->create($data);
				} else {
					$response = $api->edit($list['contacts'][0]['id'], $data);
				}
			}
		} catch (Exception $e) {
		}
	}
	
	private static function authenticateLG() {
		$auth_redirect =  admin_url('/options-general.php?page=ocrmf_options', false );
		$baseUrl = get_option('ocrmf_lg_url');
		if (empty($baseUrl)) {
			die('sdfs');
			return null;
		}
	    $clientKey = get_option('ocrmf_lg_id');
		$clientSecret = get_option('ocrmf_lg_secret');
		$token = get_option('ocrmf_lg_token');
		if (!$clientKey || !$clientSecret || !$token)
			return null;
		$settings = array(
		    'baseUrl'          => $baseUrl,
		    'version'          => 'OAuth2',
		    'clientKey'        => $clientKey,
			'clientSecret'     => $clientSecret,
			'callback'         =>  $auth_redirect,
			'accessToken' => $token['access_token'],
			'accessTokenExpires' => $token['expires'],
			'refreshToken' => $token['refresh_token'],
		);
		$initAuth = new ApiAuth();
		$auth = $initAuth->newAuth($settings);
		try {
			if ($auth->validateAccessToken()) {
				if ($auth->accessTokenUpdated()) {
					$accessTokenData = $auth->getAccessTokenData();
					update_option('ocrmf_lg_token', $accessTokenData);
				}
				$api = new MauticApi();
				$contactApi = $api->newApi('Contacts', $auth, $baseUrl . '/api');
				return $contactApi;
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	

}
