<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Event_vote_select_model extends CI_Model {
	private $table_name = 'event_vote_select_tbl';
	private $fields_pk = 'id';
	public function __construct() {
		parent::__construct ();
		// $this->load->config('set/databases_fiels', TRUE);
		$this->r_db = $this->load->database ( 'vidol_event_read', TRUE );
		$this->w_db = $this->load->database ( 'vidol_event_write', TRUE );
	}
	public function __destruct() {
		$this->r_db->close ();
		unset ( $this->r_db );
		$this->w_db->close ();
		unset ( $this->w_db );
		// parent::__destruct();
	}
	public function insert_data($data) {
		$this->w_db->insert ( $this->table_name, $data );
		$id = $this->w_db->insert_id ();
		// echo $this->w_db->last_query();
		return $id;
	}
	public function update_data($pk, $data) {
		$this->w_db->where ( $this->fields_pk, $pk );
		$this->w_db->update ( $this->table_name, $data );
		$result = $this->w_db->affected_rows ();
		// echo $this->w_db->last_query();
		return $result;
	}
	public function get_row_by_pk($select, $pk) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		$this->r_db->where ( $this->fields_pk, $pk );
		$query = $this->r_db->get ( $this->table_name );
		// echo $this->r_db->last_query();
		if ($query->num_rows () > 0) {
			return $query->row ();
		}
		return false;
	}
	//取得會員資料
	public function get_user_by_condifid_date($select, $config_id, $start_date=null, $end_date=null) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		$this->r_db->where ( 'config_id', $config_id );
		if(empty($start_date)){
			$this->r_db->where ( 'created_at >=', $start_date );
		}
		if(empty($end_date)){
			$this->r_db->where ( 'created_at <', $end_date );
		}
		$query = $this->r_db->get ( $this->table_name );
		return $query;
	}
	// 投票數
	public function get_vote_count_by_configid_date($config_id, $start_date, $end_date) {
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		$this->r_db->where ( 'created_at >=', $start_date );
		$this->r_db->where ( 'created_at <', $end_date );
		$this->r_db->from ( $this->table_name );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 第一次投票會員
	public function get_new_vote_count_by_configid_date($config_id, $start_date, $end_date) {
		// 子查詢
		$this->r_db->select ( '*, count(id) as count_no' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		$this->r_db->where ( 'created_at <', $end_date );
		$this->r_db->group_by ( 'user_id' );
		$this->r_db->order_by ( 'created_at', 'ASC' );
		$sql = $this->r_db->get_compiled_select ( $this->table_name );
		// $sql = sprintf("SELECT count(*) FROM ( %s ) as t WHERE count_no = 1 AND created_at >= '%s' AND created_at < '%s' ", $sql, $start_date, $end_date);
		// echo $sql;
		$this->r_db->where ( 'count_no', '1' );
		$this->r_db->where ( 'created_at >=', $start_date );
		$this->r_db->where ( 'created_at <', $end_date );
		$this->r_db->from ( '(' . $sql . ') as t ' );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 累計不重複投票數
	public function get_single_vote_count_by_configid_date($config_id, $start_date, $end_date) {
		// 子查詢
		$this->r_db->select ( '*, count(id) as count_no' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		$this->r_db->where ( 'created_at <', $end_date );
		$this->r_db->group_by ( 'user_id' );
		$this->r_db->order_by ( 'created_at', 'ASC' );
		$this->r_db->from ( $this->table_name );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 累計投票數
	public function get_total_vote_count_by_configid_date($config_id, $start_date, $end_date) {
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		// $this->r_db->where ( 'created_at >=', $start_date );
		$this->r_db->where ( 'created_at <', $end_date );
		$this->r_db->from ( $this->table_name );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 投票註冊數
	public function get_registered_count_by_configid_date($config_id, $start_date, $end_date) {
		// 子查詢
		$this->r_db->select ( 'datediff(`created_at`, date_add(`user_created_at`, interval 8 hour)) as user_date' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		$this->r_db->where ( 'created_at >=', $start_date );
		$this->r_db->where ( 'created_at <', $end_date );
		$sql = $this->r_db->get_compiled_select ( $this->table_name );
		$this->r_db->where ( 'user_date', '0' );
		$this->r_db->from ( '(' . $sql . ') as t ' );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 累計投票註冊數
	public function get_total_registered_count_by_configid_date($config_id, $start_date, $end_date) {
		// 子查詢
		$this->r_db->select ( 'datediff(`created_at`, date_add(`user_created_at`, interval 8 hour)) as user_date' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'data_no', '0' );
		$this->r_db->where ( 'created_at <', $end_date );
		$sql = $this->r_db->get_compiled_select ( $this->table_name );
		$this->r_db->where ( 'user_date', '0' );
		$this->r_db->from ( '(' . $sql . ') as t ' );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
}
