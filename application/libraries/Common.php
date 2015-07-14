<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Common{
	var $CI;
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model("dbHandler");
	}
	
	/**
	 * 获取一条信息
	 * return object
	 */
	public function getOneData($condition){
		$data=$this->CI->dbHandler->selectData($condition);
		if(sizeof($data)>0)
			return $data[0];
		else
			return array();
	}
	public function getOneDataById($type,$contentId){
		$condition=array(
			'table'=>$type,
			'result'=>'data',
			'where'=>array($type.'_id'=>$contentId)
		);
		return $this->getOneData($condition);
	}
	public function getOneDataAdvance($type,$where){
		$condition=array(
			'table'=>$type,
			'result'=>'data',
			'where'=>$where
		);
		return $this->getOneData($condition);
	}
	public function getData($condition){
		return $this->CI->dbHandler->selectData($condition);
	}
	public function isExist($type,$where){
		$result=false;
		$condition=array(
			'table'=>$type,
			'result'=>'data',
			'where'=>$where
		);
		$data=$this->getData($condition);
		if(sizeof($data)>0){
			$result=true;
		}
		return $result;
	}
	public function checkEmailFormat($email){
		$regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
		if (preg_match($regex, $email)) return true;
		else return false;
	}
	public function email($to,$subject,$message){
		$this->CI->load->library('email');
		//以下设置Email参数
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.163.com';
		$config['smtp_user'] = 'sunxguo@163.com';
		$config['smtp_pass'] = '19910910Mk1024';
		$config['smtp_port'] = '25';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$this->CI->email->initialize($config);
		//以下设置Email内容
		$this->CI->email->from('sunxguo@163.com', 'AiiMai');
		$this->CI->email->to($to); 
//		$this->email->cc('another@another-example.com'); 
//		$this->email->bcc('them@their-example.com'); 

		$this->CI->email->subject($subject);
		$this->CI->email->message($message); 

		$this->CI->email->send();

//		echo $this->email->print_debugger();
	}
	public function SMS($phoneNumber,$text){
		$url='http://sms.webchinese.cn/web_api/';
		$param=array(
			'Uid'=>'MonkeyKing',
			'Key'=>'916befe64d458c759a3a',
			'smsMob'=>$phoneNumber,
			'smsText'=>$text
		);
		return httpGet($url,$param,array());
	}
	/*
	public function globalSMS($phoneNumber,$text){
		$param = array (
			'src' => 'MonkeyKing', // 用户名
			'pwd' => '19910910Mk1024', // 你的密码
			'ServiceID' => 'SEND',
			'dest' => $phoneNumber, // 你的目的号码
			'sender' => '1370138', // 你的原号码
			'codec' => '8', // 编码
			'msg' => $this->encodeHexStr(8,$text)
		);
		$uri = "http://210.51.190.233:8085/mt/mt3.ashx";
		return httpPost($url,$param,array());
	}
	public function encodeHexStr($dataCoding, $realStr){
		if ($dataCoding == 15){
			return strtoupper(bin2hex(iconv('UTF-8', 'GBK', $realStr)));               
		}
		else if ($dataCoding == 3){
			return strtoupper(bin2hex(iconv('UTF-8', 'ISO-8859-1', $realStr)));               
		}
		else if ($dataCoding == 8){
			return strtoupper(bin2hex(iconv('UTF-8', 'UCS-2', $realStr)));   
		}
		else{
			return strtoupper(bin2hex(iconv('UTF-8', 'ASCII', $realStr)));
		}
	}*/
}

/* End of file Common.php */