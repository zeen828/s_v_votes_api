<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Event_vote_report_model extends CI_Model {
	private $table_name = 'event_vote_report_tbl';
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
	// 查是否重副
	public function get_count_by_configid_dateat($config_id, $date) {
		$this->r_db->select ( 'id' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'date_at', $date );
		$this->r_db->from ( $this->table_name );
		$count = $this->r_db->count_all_results ();
		// echo $this->r_db->last_query ();
		return $count;
	}
	// 更新
	public function update_data_by_configid_dateat($config_id, $date, $data) {
		$this->w_db->where ( 'config_id', $config_id );
		$this->w_db->where ( 'date_at', $date );
		$this->w_db->update ( $this->table_name, $data );
		$result = $this->w_db->affected_rows ();
		// echo $this->w_db->last_query();
		return $result;
	}
}
