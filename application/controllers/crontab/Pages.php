<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/**
 * crontab 指令
 * crontab -l 查詢任務
 * crontab -e 編輯任務
 * /etc/init.d/cron restart 重啟
 */
class Pages extends CI_Controller {
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
	// 建立暫存
	public function landing_cached() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->model ( 'vidol_event/page_landing_model' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy' 
			) );
			// 變數
			$data_cache = array ();
			// cache name key
			$data_cache ['name'] = sprintf ( '%s_landing_page', ENVIRONMENT );
			$query = $this->page_landing_model->get_query_limit ( '*', '30' );
			if ($query->num_rows () > 0) {
				foreach ( $query->result () as $row ) {
					$data_cache [$data_cache ['name']] [$row->position] [] = array (
							'id' => $row->id,
							'title' => $row->title,
							'des' => $row->des,
							'image' => $row->image,
							'url' => $row->url 
					);
				}
			}
			// 紀錄
			$status = $this->cache->memcached->save ( $data_cache ['name'], $data_cache [$data_cache ['name']], 90000 );
			$info = $this->cache->memcached->cache_info ();
			//
			unset ( $data_cache );
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
	// 建立暫存
	public function load_cached() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			$this->load->model ( 'vidol_event/page_load_model' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy'
			) );
			// 變數
			$data_cache = array ();
			// cache name key
			$data_cache ['name'] = sprintf ( '%s_load_page', ENVIRONMENT );
			$query = $this->page_load_model->get_query_limit ( '*', '30' );
			if ($query->num_rows () > 0) {
				foreach ( $query->result () as $row ) {
					$data_cache [$data_cache ['name']] [$row->position] [] = array (
							'id' => $row->id,
							'title' => $row->title,
							'des' => $row->des,
							'image' => $row->image,
							'url' => $row->url
					);
				}
			}
			// 紀錄
			$status = $this->cache->memcached->save ( $data_cache ['name'], $data_cache [$data_cache ['name']], 90000 );
			$info = $this->cache->memcached->cache_info ();
			//
			unset ( $data_cache );
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
