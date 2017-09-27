<?php

class OneCRMApiClient {

	private function getParameter($name, $defaultValue = null) {
		$name = 'ocrmf_' . $name;
		return get_option($name, $defaultValue);
	}

	private function getAuthToken() {
		return $this->getParameter('onecrm_token');
	}

	private function checkAuthCache() {
		$valid = false;
		$now = time();
		$auth_token = $this->getAuthToken();
		if ($auth_token) {
			if ($auth_token['expires_at'] > $now + 30) {
				$valid = true;
			}
		}
		if (!$valid) {
			$valid = $this->authenticate();
		}
		return $valid;
	}

	private function extractError($data) {
		if (isset($data['error'])) {
			if (isset($data['message']))
				return $data['message'];
			if (isset($data['error']['message']))
				return $data['error']['message'];
		}
	}

	private function authenticate() {
		$client_id = $this->getParameter('onecrm_id');
		$client_secret = $this->getParameter('onecrm_secret');
		$body = [
			'grant_type' => 'client_credentials',
			'client_id' => $client_id,
			'scope' => 'read write profile',
			'client_secret' => $client_secret,
		];
		$response = $this->sendRequest('/auth/user/access_token', 'POST', array(), $body, true);
		$error = $this->extractError($response);
		if ($error)
			return false;
		$response['expires_at'] = time() + $response['expires_in'];
		update_option('ocrmf_onecrm_token', $response);
		return true;
	}

	public function sendRequest($endpoint, $method = 'GET', $query = array(), $body = null, $skip_auth = false) {
		$url = $this->getParameter('onecrm_url');
		$client_id = $this->getParameter('onecrm_id');
		$client_secret = $this->getParameter('onecrm_secret');
		if (!$url || !$client_id || !$client_secret) {
			return null;
		}
		$headers = array();
		if (!$skip_auth) {
			if (!$this->checkAuthCache()) {
				return null;
			}
			$token = $this->getAuthToken();
			$headers[] = 'Authorization: Bearer ' . $token['access_token'];
		}
		$url_parts = parse_url($url);
		$scheme = isset($url_parts['scheme']) ? $url_parts['scheme'] : 'https';
		$path = isset($url_parts['path']) ? $url_parts['path'] : '/api.php';
		if (substr($path, -10) == '/index.php') {
			$path = substr($path, 0, -9);
		}
		if (substr($path, -8) != '/api.php') {
			if (substr($path, -1) != '/') {
				$path .= '/';
			}
			$path .= 'api.php';
		}
		$url = $scheme . '://' . $url_parts['host'] . $path . $endpoint;
		$method   = strtoupper($method);
		$query = http_build_query($query);
		if (strlen($query)) {
			$url .= '?' . $query;
		}
		if (!is_null($body)) {
			$body = json_encode($body);
			$headers[] = 'Content-Type: application/json';
		}
        $options = array(
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 0,
			CURLOPT_URL				=>  $url,
			CURLOPT_HTTPHEADER		=> $headers,
        );

		switch ($method) {
			case 'POST':
			case 'PUT':
			case 'PATCH':
				$options[CURLOPT_CUSTOMREQUEST] = $method;
				$options[CURLOPT_POSTFIELDS] = $body;
				break;
			case 'DELETE':
				$options[CURLOPT_CUSTOMREQUEST] = $method;
				break;

		}
		$ch = curl_init();
		foreach ($options as $k => $v)
			curl_setopt($ch, $k, $v);
		$result = curl_exec($ch);
		curl_close($ch);
		if ($result === false)
			return false;
		return json_decode($result, true);
	}
}

