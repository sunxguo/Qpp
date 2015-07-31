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
		//在“云通讯”平台注册子账号
		$subAccount=$this->common->createSubAccount($_POST['email']);
		if($subAccount->result!=0){
			$echoData->result=5;
			$echoData->data='Sub Account registration failed!'.$subAccount->data;
			echo json_encode($echoData);
			return false;
		}
		$this->dbhandler->insertData('user',array(
			'email'=>$_POST['email'],
			'password'=>md5('QppMK'.$_POST['password']),
			'gender'=>2,
			'time'=>date("Y-m-d H:i:s"),
			'subAccountSid'=>$subAccount->data->subAccountSid,
			'subToken'=>$subAccount->data->subToken,
			'dateCreated'=>$subAccount->data->dateCreated,
			'voipAccount'=>$subAccount->data->voipAccount,
			'voipPwd'=>$subAccount->data->voipPwd
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
		$this->dbhandler->updateData($updateData);
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
		$data->time=$user->time;
		$data->device=$_POST['device'];
		$data->subAccountSid=$user->subAccountSid;
		$data->subToken=$user->subToken;
		$data->dateCreated=$user->dateCreated;
		$data->voipAccount=$user->voipAccount;
		$data->voipPwd=$user->voipPwd;
		
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	//添加联系人
	public function addContact(){
		$echoData=new stdClass;
		if(!isset($_POST['token']) || !isset($_POST['contactId'])){
			$echoData->result=1;
			$echoData->data='token and contactId can not be null!';
			echo json_encode($echoData);
			return false;
		}
		$checkTokenResult=$this->common->checkToken($_POST['token']);
		if(!$checkTokenResult->result){
			$echoData->result=2;
			$echoData->data=$checkTokenResult->data;
			echo json_encode($echoData);
			return false;
		}
		$user=$checkTokenResult->data;
		$userId=$user->id;
		if(!$this->common->isExist('user',array('id'=>$_POST['contactId']))){
			$echoData->result=3;
			$echoData->data='The contact does`t exist!';
			echo json_encode($echoData);
			return false;
		}
		if($this->common->isExist('contact',array('userId'=>$userId,'contactId'=>$_POST['contactId']))){
			$echoData->result=4;
			$echoData->data='The contact has been added as a friend!';
			echo json_encode($echoData);
			return false;
		}
		$this->dbhandler->insertData('contact',array(
			'userId'=>$userId,
			'contactId'=>$_POST['contactId']
		));
		$echoData->result=0;
		$echoData->data='Added successfully!';
		echo json_encode($echoData);

	}
	//获取某个用户信息
	public function getUser(){
		$echoData=new stdClass;
		if(!isset($_GET['id']) || !isset($_GET['token'])){
			$echoData->result=1;
			$echoData->data='id and token can not be null!';
			echo json_encode($echoData);
			return false;
		}
		$checkTokenResult=$this->common->checkToken($_GET['token']);
		if(!$checkTokenResult->result){
			$echoData->result=2;
			$echoData->data=$checkTokenResult->data;
			echo json_encode($echoData);
			return false;
		}
		if(!$this->common->isExist('user',array('id'=>$_GET['id']))){
			$echoData->result=3;
			$echoData->data='This user does`t exist!';
			echo json_encode($echoData);
			return false;
		}
		$user=$this->common->getOneDataById('user',$_GET['id']);
		$echoData->result=0;
		$echoData->data=$user;
		echo json_encode($echoData);
	}
	//获取全部联系人
	public function getAllContacts(){
		$echoData=new stdClass;
		if(!isset($_GET['token'])){
			$echoData->result=1;
			$echoData->data='Token can not be null!';
			echo json_encode($echoData);
			return false;
		}
		$checkTokenResult=$this->common->checkToken($_GET['token']);
		if(!$checkTokenResult->result){
			$echoData->result=2;
			$echoData->data=$checkTokenResult->data;
			echo json_encode($echoData);
			return false;
		}
		$user=$checkTokenResult->data;
		$userId=$user->id;
		$condition=array(
			'table' => 'contact',
			'result' => 'data',
			'where' => array('userId' => $userId),
			'join' => array('user' => 'user.id = contact.contactId'),
			'order_by' => array('note' => 'asc')
		);
		$contacts=$this->common->getData($condition);
		$allContacts=array();
		foreach ($contacts as $key => $value) {
			$contact=new stdClass;
			$contact->id=$value->contactId;
			$contact->note=$value->note;
			$contact->username=$value->username;
			$contact->name=$value->name;
			$contact->email=$value->email;
			$contact->signature=$value->signature;
			$contact->avatar=$value->avatar;
			$allContacts[]= $contact;
		}
		$echoData->result=0;
		$echoData->data=$allContacts;
		echo json_encode($echoData);
	}
	//删除联系人
	public function deleteContact(){

	}
}
