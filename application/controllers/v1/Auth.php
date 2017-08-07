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
