<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('Common');
		$this->load->model("dbHandler");
	}
	public function index(){
		
		echo json_encode();
	}
	/**
	 * return //0->success 1->Null error! 2->Email format error! 3->Password length error! 4->Email has been registered!
	 **/
	public function register(){
		$echoData=new stdClass;
		if(!isset($_POST['email']) || !isset($_POST['password'])){
			$echoData->result=1;
			$echoData->data='Email and Password can not be null!';
			echo json_encode($echoData);
			return false;
		}
		if(!$this->common->checkEmailFormat($_POST['email'])){
			$echoData->result=2;
			$echoData->data='The format of email is error!';
			echo json_encode($echoData);
			return false;
		}
		$emailLength=strlen($_POST['password']);
		if($emailLength<6 || $emailLength>30){
			$echoData->result=3;
			$echoData->data='The length of password must be 6~30!';
			echo json_encode($echoData);
			return false;
		}
		if($this->common->isExist('user',array('email'=>$_POST['email']))){
			$echoData->result=4;
			$echoData->data='This email has been registered!';
			echo json_encode($echoData);
			return false;
		}
		$this->dbHandler->insertData('user',array(
			'email'=>$_POST['email'],
			'password'=>md5('QppMK'.$_POST['password']),
			'gender'=>2,
			'time'=>date("Y-m-d H:i:s")
		));
		$echoData->result=0;
		$echoData->data='Registered successfully!';
		echo json_encode($echoData);
	}
	
	/**
	 * return //0->success 1->Null error! 2->Email format error! 3->Password length error! 4->Email does not exist! 5->Password error!
	 **/
	public function login(){
		$echoData=new stdClass;
		if(!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['device'])){
			$echoData->result=1;
			$echoData->data='Email,Password,Device can not be null!';
			echo json_encode($echoData);
			return false;
		}
		if(!$this->common->checkEmailFormat($_POST['email'])){
			$echoData->result=2;
			$echoData->data='The format of email is error!';
			echo json_encode($echoData);
			return false;
		}
		$emailLength=strlen($_POST['password']);
		if($emailLength<6 || $emailLength>30){
			$echoData->result=3;
			$echoData->data='The length of password must be 6~30!';
			echo json_encode($echoData);
			return false;
		}
		$user=$this->common->getOneDataAdvance('user',array('email'=>$_POST['email']));
		if(!isset($user->email)){
			$echoData->result=4;
			$echoData->data='This email does not exist!';
			echo json_encode($echoData);
			return false;
		}
		if($user->password!=md5('QppMK'.$_POST['password'])){
			$echoData->result=5;
			$echoData->data='Password error!';
			echo json_encode($echoData);
			return false;
		}
		$token=md5(($user->username).($user->password).time()); //创建用于激活识别码 
		$tokenExptime = time()+60*60*24*30;//过期时间为30天后
		$updateData=array(
			'table'=>'user',
			'where'=>array('id'=>$user->id),
			'data'=>array('device'=>$_POST['device'],'token'=>$token,'token_exptime'=>$tokenExptime)
		);
		$this->dbHandler->updateData($updateData);
		//if(time()>$user->token_exptime)
		$data=new stdClass;
		$data->token=$token;
		$data->username=$user->username;
		$data->avatar=$user->avatar;
		$data->signature=$user->signature;
		$data->email=$user->email;
		$data->phone=$user->phone;
		$data->name=$user->name;
		$data->gender=$user->gender;
		$data->contacts=$user->contacts;
		$data->time=$user->time;
		$data->device=$user->device;
		
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
}
