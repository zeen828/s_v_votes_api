<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Event_vote_item_model extends CI_Model {
	private $table_name = 'event_vote_item_tbl';
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
	public function get_item_by_configid_status_sort($select, $config_id) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'status', '1' );
		$this->r_db->order_by ( 'group_no', 'ASC' );
		$this->r_db->order_by ( 'sort', 'ASC' );
		$query = $this->r_db->get ( $this->table_name );
		// echo $this->r_db->last_query();
		return $query;
	}
	public function get_item_sum_row_by_configid_status_group($config_id) {
		$this->r_db->select ( 'config_id, group_no, SUM(ticket) as sum_ticket, SUM(ticket_add) as sum_ticket_add' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'status', '1' );
		$this->r_db->group_by ( array (
				'config_id',
				'group_no' 
		) );
		$query = $this->r_db->get ( $this->table_name );
		// echo $this->r_db->last_query();
		return $query;
	}
	// 更新項目得票總票數
	public function update_item_ticket($config_id, $time_id) {
		// UPDATE `event_vote_item_tbl` SET ticket = (
		// SELECT SUM(ticket) as sum_ticket
		// FROM `event_vote_select_tbl`
		// WHERE `config_id` = '1'
		// AND `item_id` = '9'
		// GROUP BY `config_id`, `item_id`
		// ) WHERE `id` = '9'
		$this->r_db->select ( 'SUM(ticket) as sum_ticket' );
		$this->r_db->where ( 'config_id', $config_id );
		$this->r_db->where ( 'item_id', $time_id );
		$this->r_db->group_by ( array (
				'config_id',
				'item_id' 
		) );
		$sql = $this->r_db->get_compiled_select ( 'event_vote_select_tbl' );
		// echo $sql;
		$this->w_db->set ( 'ticket', '( ' . $sql . ' )', false );
		$this->w_db->where ( $this->fields_pk, $time_id );
		$this->w_db->update ( $this->table_name );
		$result = $this->w_db->affected_rows ();
		// echo $this->w_db->last_query();
		return $result;
	}
}
