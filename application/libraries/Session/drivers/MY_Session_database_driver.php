<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Session_database_driver extends CI_Session_database_driver
{

    public function __construct (&$params)
    {
        parent::__construct($params);
        
        $CI = & get_instance();
        isset($CI->db) or $CI->load->database('write', true);
        $this->_db = $CI->db;
        
        if (! $this->_db instanceof CI_DB_query_builder) {
            throw new Exception('Query Builder not enabled for the configured database. Aborting.');
        } elseif ($this->_db->pconnect) {
            throw new Exception('Configured database connection is persistent. Aborting.');
        } elseif ($this->_db->cache_on) {
            throw new Exception('Configured database connection has cache enabled. Aborting.');
        }
        
        $db_driver = $this->_db->dbdriver . (empty($this->_db->subdriver) ? '' : '_' . $this->_db->subdriver);
        if (strpos($db_driver, 'mysql') !== FALSE) {
            $this->_platform = 'mysql';
        } elseif (in_array($db_driver, array(
                'postgre',
                'pdo_pgsql'
        ), TRUE)) {
            $this->_platform = 'postgre';
        }
        
        // Note: BC work-around for the old 'sess_table_name' setting, should be
        // removed in the future.
        isset($this->_config['save_path']) or $this->_config['save_path'] = config_item('sess_table_name');
    }
}
