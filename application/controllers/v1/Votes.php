<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" ); // On, Off
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS' );
header( 'Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept' );
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
			// Debug info
			$data_input ['debug'] = $this->get ( 'debug' );
			if ($data_input ['debug'] == 'debug') {
				$this->data_result ['debug'] ['input'] = &$data_input;
				$this->data_result ['debug'] ['cache'] = &$data_cache;
			}
			// 必填檢查
			if (empty ( $data_input ['config_id'] )) {
				// 必填錯誤
				$this->data_result ['message'] = $this->lang->line ( 'input_required_error' );
				$this->data_result ['code'] = $this->config->item ( 'input_required_error' );
				$this->response ( $this->data_result, 416 );
				return;
			}
			// cache name key
			$data_cache ['name'] = sprintf ( '%s_event_vote_%d', ENVIRONMENT, $data_input ['config_id'] );
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
	public function vote_post() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->config->load ( 'restful_status_code' );
			$this->load->model ( 'postgre/token_model' );
			$this->load->model ( 'vidol_event/event_vote_select_model' );
			$this->lang->load ( 'restful_status_lang', 'traditional-chinese' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy' 
			) );
			// 變數
			$data_input = array ();
			$data_cache = array ();
			$date_user = false;
			$date_config = false;
			$this->data_result = array (
					'result' => array (),
					'code' => $this->config->item ( 'system_default' ),
					'message' => '',
					'time' => 0 
			);
			// 接收變數
			$data_input ['date'] = date ( 'Y-m-d' );
			$data_input ['now_datetime'] = date ( 'Y-m-d H:i:s' );
			$data_input ['random'] = $this->post ( 'random' );
			$data_input ['token'] = $this->post ( 'token' );
			$data_input ['config_id'] = $this->post ( 'config_id' );
			$data_input ['item_id'] = $this->post ( 'item_id' );
			// Debug info
			$data_input ['debug'] = $this->post ( 'debug' );
			if ($data_input ['debug'] == 'debug') {
				$this->data_result ['debug'] ['input'] = &$data_input;
				$this->data_result ['debug'] ['cache'] = &$data_cache;
				$this->data_result ['debug'] ['config'] = &$date_config;
				$this->data_result ['debug'] ['user'] = &$date_user;
			}
			// 必填檢查
			if (empty ( $data_input ['random'] ) || empty ( $data_input ['token'] ) || empty ( $data_input ['config_id'] ) || empty ( $data_input ['item_id'] )) {
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
			if ($time_gap > 120) {
				// 預時120秒
				$this->data_result ['message'] = $this->lang->line ( 'system_time_out' );
				$this->data_result ['code'] = $this->config->item ( 'system_time_out' );
				// 必填錯誤標記
				$this->benchmark->mark ( 'error_timeout' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_timeout' );
				$this->response ( $this->data_result, 408 );
				return;
			}
			// 有無投票活動設定
			$data_cache ['config_name'] = sprintf ( '%s_event_vote_%d', ENVIRONMENT, $data_input ['config_id'] );
			$data_cache [$data_cache ['config_name']] = $this->cache->memcached->get ( $data_cache ['config_name'] );
			if ($data_cache [$data_cache ['config_name']] == false) {
				// 無活動
				$this->data_result ['message'] = $this->lang->line ( 'system_not_config' );
				$this->data_result ['code'] = $this->config->item ( 'system_not_config' );
				// 無活動標記
				$this->benchmark->mark ( 'error_not_config' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_not_config' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			// 有無過期
			$date_config = $data_cache [$data_cache ['config_name']];
			if ($date_config ['start'] > $data_input ['now_datetime'] || $data_input ['now_datetime'] > $date_config ['end']) {
				// 活動尚未開始
				$this->data_result ['message'] = $this->lang->line ( 'system_expired' );
				$this->data_result ['code'] = $this->config->item ( 'system_expired' );
				// 活動尚未開始標記
				$this->benchmark->mark ( 'error_expired' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_expired' );
				$this->response ( $this->data_result, 401 );
				return;
			}
			// 取得token轉換user資料
			$date_user = $this->token_model->get_user_row_by_token ( 'identities.*', $data_input ['token'] );
			if ($date_user == false) {
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
			$data_cache ['user_name'] = sprintf ( '%s_event_vote_user_%s', ENVIRONMENT, $date_user->uid );
			// $this->cache->memcached->delete ( $data_cache['name_1'] );
			$data_cache [$data_cache ['user_name']] = $this->cache->memcached->get ( $data_cache ['user_name'] );
			if ($data_cache [$data_cache ['user_name']] != false && isset ( $data_cache [$data_cache ['user_name']] [$data_input ['date']] )) {
				// 今天投票過
				$this->data_result ['message'] = $this->lang->line ( 'event_repeat' );
				$this->data_result ['code'] = $this->config->item ( 'event_repeat' );
				// 今天投票過標記
				$this->benchmark->mark ( 'error_repeat' );
				$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'error_repeat' );
				$this->response ( $this->data_result, 405 );
				return;
			}
			// 投票資料
			$data_post = array (
					'config_id' => $data_input ['config_id'],
					'item_id' => $data_input ['item_id'],
					'user_id' => $date_user->uid,
					'user_created_at' => $date_user->created_at,
					'ticket' => 1,
					'year_at' => date ( 'Y' ),
					'month_at' => date ( 'm' ),
					'day_at' => date ( 'd' ),
					'hour_at' => date ( 'h' ),
					'minute_at' => date ( 'i' ),
					'created_at' => $data_input ['now_datetime'] 
			);
			$status = $this->event_vote_select_model->insert_data ( $data_post );
			if ($status == true) {
				if (isset ( $data_cache [$data_cache ['user_name']] [$data_input ['date']] )) {
					$data_cache [$data_cache ['user_name']] [$data_input ['date']] = $data_cache [$data_cache ['user_name']] [$data_input ['date']] + 1;
				} else {
					$data_cache [$data_cache ['user_name']] [$data_input ['date']] = 1;
				}
			}
			// 紀錄
			$status = $this->cache->memcached->save ( $data_cache ['user_name'], $data_cache [$data_cache ['user_name']], 90000 ); // 25小時90000秒
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
