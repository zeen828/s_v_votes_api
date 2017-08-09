<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" ); // On, Off
require_once APPPATH . '/libraries/MY_REST_Controller.php';
class Votes extends MY_REST_Controller {
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
	public function test_get() {
		$this->output->enable_profiler(TRUE);
		$this->load->database ( 'postgre_read' );
		$tables = $this->db->list_tables();
		foreach ($tables as $table)
		{
		        echo $table, "<br/>\n";
		}

		$this->db->where ( 'token', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZGVudGl0eSI6eyJpZCI6NjIxMjM1LCJ1aWQiOiJ2UVVwMnNrcWNGIiwiZW1haWxfdmVyaWZpZWQiOnRydWV9LCJhcHBsaWNhdGlvbl9pZCI6MSwiZXhwaXJlc19hdCI6MTUwMjQyMDc0NywicmFuZF9rZXkiOiI4YTJlNDBmOWYwMmVmNjhiYThhYzQxOWIyNmYwNDE1NCJ9.afK2HdCA3TuSXAdTCBSNIvXT7TIdyFFoF6onToco0ZuPCHchl1Rmb3DHDVnHGeyCqf3sYr4m7ukL6lV40gN1DA' );
		$query = $this->db->get ( 'oauth_access_tokens' );
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				print_r($row);
			}
		}
	}
	public function vote_get() {
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
			$data_input ['config_id'] = $this->get ( 'config_id' );
			// 必填檢查
			if (empty ( $data_input ['config_id'] )) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// cache name key
			$data_cache [ 'name' ] = sprintf('%s_event_vote_%d', ENVIRONMENT, $data_input ['config_id']);
			// $this->cache->memcached->delete ( $data_cache['name_1'] );
			$data_cache [ $data_cache [ 'name' ] ] = $this->cache->memcached->get ( $data_cache [ 'name' ] );
			if ($data_cache [$data_cache['name']] == false) {
				$data_cache [ $data_cache [ 'name' ] ] = $this->cache->memcached->get ( $data_cache [ 'name' ] );
			}
			//
			$this->data_result [ 'result' ] = $data_cache [ $data_cache [ 'name' ] ];
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
			$this->response ( $this->data_result, 200 );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
	public function vote_post() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->config->load ( 'restful_status_code' );
			$this->load->model ( 'postgre/token_model' );
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
			$data_input ['token'] = $this->post ( 'token' );
			$data_input ['config_id'] = $this->post ( 'config_id' );
			$data_input ['item_id'] = $this->post ( 'item_id' );
			// 必填檢查
			if ( empty ( $data_input ['token'] ) || empty ( $data_input ['config_id'] ) || empty ( $data_input ['item_id'] ) ) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_required' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_required' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// 取得token轉換資料
			$token = $this->token_model->get_oauth_access_tokens_by_token_or_refreshtoken('*', $data_input ['token'], null);
			print_r($token);

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
