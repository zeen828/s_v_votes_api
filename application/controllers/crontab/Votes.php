<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
	}
	// 統計
	public function cached()
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
			$data_cache = array ();
			//取得所有活動設定
			$query = $this->event_vote_config_model->get_query_by_status_at('*');
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					print_r($row);
					$vote_config[$row->id] = $row;
					unset($row);
				}
			}
			unset($query);
			if( count( $vote_config ) >= 1 ){
				foreach ($vote_config as $key => $value) {
					//
					$cache_name = sprintf('%s_event_vote_%d', ENVIRONMENT, $value->id);
					$data_cache[$cache_name] = array(
						'id'=>$value->id,
						'title'=>$value->title,
						'des'=>$value->des,
						'item'=>array(),
					);
					$query = $this->event_vote_item_model->get_query_by_configid_status_sort('*', $value->id);
					if ($query->num_rows() > 0) {
						foreach ($query->result() as $row) {
							print_r($row);
							$data_cache[$cache_name]['item'][] = array(
								'id'=>$row->id,
								'group'=>$row->group_no,
								'sort'=>$row->sort,
								'title'=>$row->title,
								'des'=>$row->des,
								'img'=>$row->img_url,
								'url'=>$row->click_url,
								'proportion'=>$row->proportion,
							);
							unset($row);
						}
					}
					// 紀錄
					print_r($cache_name);
					print_r($data_cache[$cache_name]);
					$status = $this->cache->memcached->save ( $cache_name, $data_cache[$cache_name], 3000 );
					print_r($status);
					$info = $this->cache->memcached->cache_info ();
					print_r($info);
					unset($query);
					unset($cache_name);
					unset($value);
				}
			}
			unset($query);
			print_r($data_cache);
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
