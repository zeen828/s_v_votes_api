<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" ); // On, Off
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS' );
header( 'Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept' );
require_once APPPATH . '/libraries/MY_REST_Controller.php';
class Auth extends MY_REST_Controller {
	private $data_debug;
	private $data_result;
	public function __construct() {
		parent::__construct ();
		$this->_my_logs_start = true;
		$this->_my_logs_type = 'auth';
		$this->data_debug = true;
		// 資料庫
		// $this->load->database ( 'vidol_billing_write' );
		// 效能檢查
		// $this->output->enable_profiler(TRUE);
	}
	public function __destruct() {
		parent::__destruct ();
		unset ( $this->data_debug );
		unset ( $this->data_result );
	}
	public function vidol_post() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->library ( 'vidol/middle_layer_api' );
			$this->config->load ( 'restful_status_code' );
			$this->lang->load ( 'restful_status_lang', 'traditional-chinese' );
			// 變數
			$data_input = array ();
			$this->data_result = array (
					'result' => array (),
					'code' => $this->config->item ( 'system_default' ),
					'message' => '',
					'time' => 0 
			);
			// 接收變數
			$data_input ['random'] = $this->post ( 'random' );
			$data_input ['username'] = $this->post ( 'username' );
			$data_input ['password'] = $this->post ( 'password' );
			// Debug info
			$data_input ['debug'] = $this->post ( 'debug' );
			if ($data_input ['debug'] == 'debug') {
				$this->data_result ['debug'] ['input'] = &$data_input;
			}
			// 必填檢查
			if (empty ( $data_input ['random'] ) || empty ( $data_input ['username'] ) || empty ( $data_input ['password'] )) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_required' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_required' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// 時間檢查(不可超過120秒)
			$datetime = substr ( time (), 0, 10 );
			$from_datetime = substr ( $data_input ['random'], 0, 10 );
			$time_gap = $datetime - $from_datetime;
			if ($time_gap > 300) {
				// 預時120秒
				$this->data_result ['message'] = $this->lang->line ( 'system_time_out' );
				$this->data_result ['code'] = $this->config->item ( 'system_time_out' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_timeout' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_timeout' );
				$this->response ( $this->data_result, 408 );
				return;
			}
			$output = $this->middle_layer_api->login_vidol ( $data_input ['username'], $data_input ['password'] );
			$this->data_result ['result'] = $output;
			if (isset ( $output->message ) && ! empty ( $output->message )) {
				// API有錯誤訊息
				$this->data_result ['message'] = $this->lang->line ( 'user_error' );
				$this->data_result ['code'] = $this->config->item ( 'user_error' );
				// 登入錯誤標記
				$this->benchmark->mark ( 'error_login' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_login' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
			$this->response ( $this->data_result, 200 );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
	public function facebook_post() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->library ( 'vidol/middle_layer_api' );
			$this->config->load ( 'restful_status_code' );
			$this->lang->load ( 'restful_status_lang', 'traditional-chinese' );
			// 變數
			$data_input = array ();
			$this->data_result = array (
					'result' => array (),
					'code' => $this->config->item ( 'system_default' ),
					'message' => '',
					'time' => 0 
			);
			// 接收變數
			$data_input ['random'] = $this->post ( 'random' );
			$data_input ['uid'] = $this->post ( 'uid' );
			$data_input ['facebook_token'] = $this->post ( 'facebook_token' );
			$data_input ['expiration'] = $this->post ( 'expiration' );
			// Debug info
			$data_input ['debug'] = $this->post ( 'debug' );
			if ($data_input ['debug'] == 'debug') {
				$this->data_result ['debug'] ['input'] = &$data_input;
			}
			// 必填檢查
			if (empty ( $data_input ['random'] ) || empty ( $data_input ['uid'] ) || empty ( $data_input ['facebook_token'] ) || empty ( $data_input ['expiration'] )) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_required' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_required' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// 時間檢查(不可超過120秒)
			$datetime = substr ( time (), 0, 10 );
			$from_datetime = substr ( $data_input ['random'], 0, 10 );
			$time_gap = $datetime - $from_datetime;
			if ($time_gap > 300) {
				// 預時120秒
				$this->data_result ['message'] = $this->lang->line ( 'system_time_out' );
				$this->data_result ['code'] = $this->config->item ( 'system_time_out' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_timeout' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_timeout' );
				$this->response ( $this->data_result, 408 );
				return;
			}
			// ML API FB login
			$output = $this->middle_layer_api->login_facebook ( $data_input ['uid'], $data_input ['facebook_token'], $data_input ['expiration'] );
			$this->data_result ['result'] = $output;
			if (isset ( $output->message ) && ! empty ( $output->message )) {
				// API有錯誤訊息
				$this->data_result ['message'] = $this->lang->line ( 'user_error' );
				$this->data_result ['code'] = $this->config->item ( 'user_error' );
				// 登入錯誤標記
				$this->benchmark->mark ( 'error_login' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_login' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
			$this->response ( $this->data_result, 200 );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
