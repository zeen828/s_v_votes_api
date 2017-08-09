<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Middle_layer_api {
	private $CI;
	private $data_result;
	public function __construct() {
		$this->CI = & get_instance ();
	}
	public function debug() {
	}
	public function login_vidol($username, $password) {
		$this->CI->config->load ( 'ml_api' );
		// 登入API
		$ch = curl_init ();
		$curl_url = sprintf ( 'http://%s/v1/oauth/token', $this->CI->config->item ( 'ml_api_domain' ) );
		$curl_header = array (
				'Content-Type: application/x-www-form-urlencoded',
				sprintf ( 'Authorization: %s', $this->CI->config->item ( 'ml_api_basic_token' ) ) 
		);
		$curl_post = http_build_query ( array (
				'grant_type' => 'password',
				'username' => $username,
				'password' => $password 
		) );
		curl_setopt ( $ch, CURLOPT_URL, $curl_url );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $curl_header );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curl_post );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$this->data_result = json_decode ( $output );
		return $this->data_result;
	}
	public function login_facebook($uid, $facebook_token, $expiration) {
		$this->CI->config->load ( 'ml_api' );
		// 登入API
		$ch = curl_init ();
		$curl_url = sprintf ( 'http://%s/v1/oauth/token', $this->CI->config->item ( 'ml_api_domain' ) );
		$curl_header = array (
				'Content-Type: application/x-www-form-urlencoded',
				sprintf ( 'Authorization: %s', $this->CI->config->item ( 'ml_api_basic_token' ) ) 
		);
		$curl_post = http_build_query ( array (
				'grant_type' => 'password',
				'user_id' => $uid,
				'facebook_token' => $facebook_token,
				'expiration_date' => $expiration 
		) );
		curl_setopt ( $ch, CURLOPT_URL, $curl_url );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $curl_header );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curl_post );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$output = json_decode ( $output );
		return $output;
	}
}
