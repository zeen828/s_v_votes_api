<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/**
 * crontab 指令
 * crontab -l 查詢任務
 * crontab -e 編輯任務
 * /etc/init.d/cron restart 重啟
 */
class Votes extends CI_Controller {
	private $data_debug;
	private $data_result;
	public function __construct() {
		parent::__construct ();
		$this->data_debug = true;
		// 效能檢查
		$this->output->enable_profiler ( TRUE );
	}
	public function __destruct() {
		unset ( $this->data_debug );
		unset ( $this->data_result );
	}
	/**
	 * 更新項目票數
	 */
	public function update_vote_item() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->model ( 'vidol_event/event_vote_config_model' );
			$this->load->model ( 'vidol_event/event_vote_item_model' );
			// 取得所有活動設定
			$query_config = $this->event_vote_config_model->get_query_by_status_at ( '*' );
			if ($query_config->num_rows () > 0) {
				foreach ( $query_config->result () as $row_config ) {
					// print_r($row_config);
					// 算得票比例用
					$ticket_total = array ();
					$query_group_sum = $this->event_vote_item_model->get_item_sum_row_by_configid_status_group ( $row_config->id );
					if ($query_group_sum->num_rows () > 0) {
						foreach ( $query_group_sum->result () as $row_group_sum ) {
							// group總票數
							$ticket_total [$row_group_sum->group_no] = $row_group_sum->sum_ticket + $row_group_sum->sum_ticket_add;
							unset ( $row_group_sum );
						}
					}
					unset ( $query_group_sum );
					// print_r ( $ticket_total );
					// 取得config_id活動項目
					$query_item = $this->event_vote_item_model->get_item_by_configid_status_sort ( '*', $row_config->id );
					if ($query_item->num_rows () > 0) {
						foreach ( $query_item->result () as $row_item ) {
							// print_r ( $row_item );
							// 票數
							$status = $this->event_vote_item_model->update_item_ticket ( $row_config->id, $row_item->id );
							echo $row_config->title, ' - ', $row_item->title, ' - ', '票數統計[', $status, ']<br/>';
							// 得票率
							$ticket_sum = $row_item->ticket + $row_item->ticket_add;
							if (! isset ( $ticket_total [$row_item->group_no] ) || empty ( $ticket_total [$row_item->group_no] ) || empty ( $ticket_sum )) {
								$proportion = '0.00';
							} else {
								$proportion = ($ticket_sum / $ticket_total [$row_item->group_no]) * 100;
							}
							$status = $this->event_vote_item_model->update_data ( $row_item->id, array (
									'proportion' => $proportion 
							) );
							echo $row_config->title, ' - ', $row_item->title, ' - ', '得票率統計[', $status, ']<br/>';
							// print_r($proportion);
							unset ( $proportion );
							unset ( $row_item );
						}
					}
					unset ( $query_item );
					unset ( $row_config );
				}
			}
			unset ( $query_config );
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
	/**
	 * 資料做cached
	 */
	public function cached() {
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
			$data_cache = array ();
			// 取得所有活動設定
			$query = $this->event_vote_config_model->get_query_by_status_at ( '*' );
			if ($query->num_rows () > 0) {
				foreach ( $query->result () as $row ) {
					$vote_config [$row->id] = $row;
					unset ( $row );
				}
			}
			unset ( $query );
			if (count ( $vote_config ) >= 1) {
				foreach ( $vote_config as $key => $value ) {
					//
					$cache_name = sprintf ( '%s_event_vote_%d', ENVIRONMENT, $value->id );
					$data_cache [$cache_name] = array (
							'config_id' => $value->id,
							'title' => $value->title,
							'des' => $value->des,
							'login' => $value->login_where,
							'vote' => $value->vote_where,
							'vote_int' => $value->vote_int,
							'lottery_int' => $value->lottery_int,
							'start' => $value->start_at,
							'end' => $value->end_at,
							'item' => array () 
					);
					$query = $this->event_vote_item_model->get_item_by_configid_status_sort ( '*', $value->id );
					if ($query->num_rows () > 0) {
						foreach ( $query->result () as $row ) {
							$data_cache [$cache_name] ['item'] [] = array (
									'item_id' => $row->id,
									'group_no' => $row->group_no,
									'group_title' => $row->group_title,
									'sort' => $row->sort,
									'title' => $row->title,
									'des' => $row->des,
									'img' => $row->img_url,
									'url' => $row->click_url,
									'proportion' => $row->proportion 
							);
							unset ( $row );
						}
					}
					unset ( $query );
					// 紀錄
					$status = $this->cache->memcached->save ( $cache_name, $data_cache [$cache_name], 90000 );
					print_r ( $data_cache [$cache_name] );
					$info = $this->cache->memcached->cache_info ();
					print_r ( $info );
					unset ( $info );
					unset ( $status );
					unset ( $query );
					unset ( $cache_name );
					unset ( $value );
				}
			}
			unset ( $data_cache );
			unset ( $vote_config );
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
