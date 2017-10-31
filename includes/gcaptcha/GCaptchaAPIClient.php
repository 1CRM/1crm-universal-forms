<?php

class GCaptchaAPIClient {

	public function __construct($secret) {
		$this->secret = $secret;
	}


	public function validate($response, $ip = null) {
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$method = 'POST';
		$body = array(
			'secret' => $this->secret,
			'response' => $response,
		);
		if ($ip) {
			$body['remoteip'] = $ip;
		}
		$body = http_build_query($body);
        $options = array(
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 0,
			CURLOPT_URL				=>  $url,
			//CURLOPT_HTTPHEADER		=> $headers,
        );

		$options[CURLOPT_CUSTOMREQUEST] = $method;
		$options[CURLOPT_POSTFIELDS] = $body;
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

