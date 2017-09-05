<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" ); // On, Off
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS' );
header( 'Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept' );
require_once APPPATH . '/libraries/MY_REST_Controller.php';
class Pages extends MY_REST_Controller {
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
	public function load_get() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->config->load ( 'restful_status_code' );
			$this->lang->load ( 'restful_status_lang', 'traditional-chinese' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy' 
			) );
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
			// Debug info
			$data_input ['debug'] = $this->get ( 'debug' );
			if ($data_input ['debug'] == 'debug') {
				$this->data_result ['debug'] ['input'] = &$data_input;
				$this->data_result ['debug'] ['cache'] = &$data_cache;
			}
			// cache name key
			$data_cache ['name'] = sprintf ( '%s_load_page', ENVIRONMENT );
			// $this->cache->memcached->delete ( $data_cache['name_1'] );
			$data_cache [$data_cache ['name']] = $this->cache->memcached->get ( $data_cache ['name'] );
			if ($data_cache [$data_cache ['name']] == false) {
				$data_cache [$data_cache ['name']] = $this->cache->memcached->get ( $data_cache ['name'] );
			}
			//
			$this->data_result ['result'] = $data_cache [$data_cache ['name']];
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
