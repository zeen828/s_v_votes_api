<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Votes extends CI_Controller {
	private $data_debug;
	private $data_result;
	public function __construct() {
		parent::__construct ();
		$this->data_debug = true;
		// 效能檢查
		$this->output->enable_profiler(TRUE);
	}
	public function __destruct() {
		parent::__destruct ();
		unset ( $this->data_debug );
		unset ( $this->data_result );
	}
	// 統計
	public function statistics()
	{
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->model ( 'vidol_event/event_vote_config_model' );
			$this->load->model ( 'vidol_event/event_vote_item_model' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy' 
			) );
			// 變數
			$vote_config = array ();
			//取得所有活動設定
			$query = $this->event_vote_config_model->get_query_by_status_at('*');
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$vote_config[$row->id] = $row;
					unset($row);
				}
			}
			unset($query);
			if( count( $vote_config ) >= 1 ){
				foreach ($vote_config as $key => $value) {
					$query = $this->event_vote_item_model->get_query_by_configid_status_sort('*', $value->id);
					if ($query->num_rows() > 0) {
						foreach ($query->result() as $row) {
							$value['row'][] = $row;
						}
					}
				}
			}
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
			print_r($this->data_result);
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
