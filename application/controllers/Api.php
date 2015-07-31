<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Api extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('Common');
//		$this->load->model("dbhandler");
	}
	public function index(){
		
		echo json_encode();
	}
	/**
	
}
