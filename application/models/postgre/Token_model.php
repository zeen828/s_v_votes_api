<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Token_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
		$this->r_db = $this->load->database ( 'postgre_read', TRUE );
		$this->w_db = $this->load->database ( 'postgre_read', TRUE );
	}
	public function __destruct() {
		$this->r_db->close ();
		unset ( $this->r_db );
		$this->w_db->close ();
		unset ( $this->w_db );
		// parent::__destruct();
	}
	public function get_oauth_access_tokens_by_token($select, $token) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		$this->r_db->where ( 'token', $token );
		$query = $this->r_db->get ( 'oauth_access_tokens' );
		// echo $this->r_db->last_query();
		if ($query->num_rows () > 0) {
			return $query->row ();
		}
		return false;
	}
	public function get_identities_by_pk($select, $pk) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		$this->r_db->where ( 'id', $pk );
		$query = $this->r_db->get ( 'identities' );
		// echo $this->r_db->last_query();
		if ($query->num_rows () > 0) {
			return $query->row ();
		}
		return false;
	}

}
