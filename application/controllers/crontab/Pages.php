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
	public function load_cached() {
		try {
			// 開始時間標記
			$this->benchmark->mark ( 'code_start' );
			// 引入
			//$this->load->model ( 'vidol_event/event_vote_config_model' );
			$this->load->driver ( 'cache', array (
					'adapter' => 'memcached',
					'backup' => 'dummy' 
			) );
			// 變數
			$data_cache = array ();
			// cache name key
			$data_cache ['name'] = sprintf ( '%s_load_page', ENVIRONMENT );
			$data_cache [$data_cache ['name']] = array (
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					),
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					),
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					),
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					),
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					),
					array (
							'id' => '',
							'title' => '',
							'des' => '',
							'image' => '',
							'url' => '' 
					) 
			);
			// 紀錄
			$status = $this->cache->memcached->save ( $data_cache ['name'], $data_cache [$data_cache ['name']], 90000 );
			$info = $this->cache->memcached->cache_info ();
			//
			unset ( $data_cache );
			// 結束時間標記
			$this->benchmark->mark ( 'code_end' );
			// 標記時間計算
			$this->data_result ['time'] = $this->benchmark->elapsed_time ( 'code_start', 'code_end' );
			//
			$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $this->data_result ) );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
