<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mongo_users_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'mongo_db' );
	}
	public function __destruct() {
		// parent::__destruct();
	}
	public function get_mongo_id_by_member_id($member_id) {
		$user = $this->mongo_db->select ( array (
				'_id',
				'member_id' 
		) )->where ( array (
				'member_id' => $member_id 
		) )->get ( '_User' );
		return array_shift ( $user );
	}
	public function get_member_id_by_mongo_id($mongo_id) {
		$user = $this->mongo_db->select ( array (
				'_id',
				'member_id' 
		) )->where ( array (
				'_id' => $mongo_id 
		) )->get ( '_User' );
		return array_shift ( $user );
	}
}


