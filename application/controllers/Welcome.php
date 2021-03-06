<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function test()
	{
		$this->load->model ( 'mongo/mongo_users_model' );
		$user = $this->mongo_users_model->get_mongo_id_by_member_id('ITP833');
		print_r($user);
		$user = $this->mongo_users_model->get_member_id_by_mongo_id('vQUp2skqcF');
		print_r($user);
	}
}
