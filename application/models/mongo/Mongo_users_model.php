<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mongo_users_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'mongo_db' );
		// $this->r_db = $this->load->library('mongo_db');
	}
	public function __destruct() {
		// unset ( $this->r_db );
		// parent::__destruct();
	}
	public function test() {
		$user_count = $this->mongo_db->count ( '_User' );
		echo $user_count;
	}
}
