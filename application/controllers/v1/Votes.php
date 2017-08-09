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
			$data_input ['date'] = date('Y-m-d');
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
			// 取得token轉換user資料
			$user = $this->token_model->get_user_row_by_token( 'identities.*', $data_input ['token'] );
			if ($user == false) {
				// 會員檢查錯誤
				$this->data_result ['message'] = $this->lang->line ( 'permissions_middle_layer_token_error' );
				$this->data_result ['code'] = $this->config->item ( 'permissions_middle_layer_token_error' );
				// 會員檢查錯誤標記
				$this->benchmark->mark ( 'error_token' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_token' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			// cache name key
			$data_cache [ 'name' ] = sprintf('%s_event_vote_%s', ENVIRONMENT, $user->uid );
			// $this->cache->memcached->delete ( $data_cache['name_1'] );
			$data_cache [ $data_cache [ 'name' ] ] = $this->cache->memcached->get ( $data_cache [ 'name' ] );
			// debug
			$this->data_result ['input'] = $data_input;
			$this->data_result ['cache'] = $data_cache;
			$this->data_result ['user'] = $user;
			// debug
			if ($data_cache [$data_cache['name']] != false && isset( $data_cache [$data_cache['name']][$data_input ['date']])) {
				// 投票過
				$this->data_result ['message'] = $this->lang->line ( 'permissions_error' );
				$this->data_result ['code'] = $this->config->item ( 'permissions_error' );
				// 投票過標記
				$this->benchmark->mark ( 'error_token' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_token' );
				$this->response ( $this->data_result, 405 );
				return;
			}
			$data_cache [$data_cache['name']][$data_input ['date']] = true;
			// 紀錄
			$status = $this->cache->memcached->save ( $data_cache [ 'name' ], $data_cache [$data_cache['name']], 90000 );//25小時90000秒
			$info = $this->cache->memcached->cache_info ();
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
