<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" ); // On, Off
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
	public function login_post() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->config->load ( 'ml_api' );
			$this->config->load ( 'restful_status_code' );
			$this->lang->load ( 'restful_status_lang', 'traditional-chinese' );
			// 變數
			$data_input = array ();
			$data_cache = array ();
			$this->data_result = array (
					'result' => array (),
					'code' => $this->config->item ( 'system_default' ),
					'message' => '',
					'time' => 0 
			);
			// 接收變數
			$data_input ['username'] = $this->post ( 'username' );
			$data_input ['password'] = $this->post ( 'password' );
			// 必填檢查
			if (empty ( $data_input ['username'] ) && empty ( $data_input ['password'] )) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// 登入API
			$ch = curl_init();
			$curl_url = sprintf('http://%s/v1/oauth/token', $this->config->item ( 'ml_api_domain' ));
			$curl_header = array(
				'Content-Type: application/x-www-form-urlencoded',
				sprintf('Authorization: %s', $this->config->item ( 'ml_api_basic_token' ))
			);
			$curl_post = http_build_query(array(
				'grant_type'=>'password',
				'username'=>$data_input ['username'],
				'password'=>$data_input ['password']
			));
			curl_setopt($ch, CURLOPT_URL, $curl_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($output);
			print_r($output);
			if(!empty($output->message)){
				// API有錯誤訊息
				$this->data_result ['message'] = $this->lang->line ( 'user_error' );
				$this->data_result ['code'] = $this->config->item ( 'user_error' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			$this->data_result ['result'] = $output;
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
