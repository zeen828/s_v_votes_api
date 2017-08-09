<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Token_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
		$this->r_db = $this->load->database ( 'postgre_production_read', TRUE );
		$this->w_db = $this->load->database ( 'postgre_production_write', TRUE );
	}
	public function __destruct() {
		$this->r_db->close ();
		unset ( $this->r_db );
		$this->w_db->close ();
		unset ( $this->w_db );
		// parent::__destruct();
	}
	public function get_oauth_access_tokens_by_token_or_refreshtoken($select, $token=null, $refresh_token=null) {
		if (! empty ( $select )) {
			$this->r_db->select ( $select );
		}
		if (! empty ( $token )) {
			$this->w_db->where ( 'token', $token );
		}
		if (! empty ( $refresh_token )) {
			$this->w_db->where ( 'refresh_token', $refresh_token );
		}
		$query = $this->r_db->get ( 'oauth_access_tokens' );
		// echo $this->r_db->last_query();
		return $query;
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
