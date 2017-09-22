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
		$user = $this->mongo_db->where ( array (
				'member_id' => $member_id 
		) )->get ( '_User' );
		return $user;
	}
}
