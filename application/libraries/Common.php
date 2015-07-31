<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Common{
	var $CI;
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model("base");
	}
	
	/**
	 * 获取一条信息
	 * return object
	 */
	public function getOneData($condition){
		$data=$this->CI->base->selectData($condition);
		if(sizeof($data)>0)
			return $data[0];
		else
			return array();
	}
	public function getOneDataById($type,$id){
		$condition=array(
			'table'=>$type,
			'result'=>'data',
			'where'=>array('id'=>$id)
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
		return $this->CI->base->selectData($condition);
	}
	public function isExist($table,$where){
		$result=false;
		$condition=array(
			'table'=>$table,
			'result'=>'data',
			'where'=>$where
		);
		$data=$this->getData($condition);
		if(sizeof($data)>0){
			$result=true;
		}
		return $result;
	}
	public function checkToken($token){
		$echoData=new stdClass;
		if(!$this->isExist('user',array('token'=>$token))){
			$echoData->result=false;
			$echoData->data='This token is wrong!';
			return $echoData;
		}
		$user=$this->getOneDataAdvance('user',array('token'=>$token));
		if(time()>$user->token_exptime){
			$echoData->result=false;
			$echoData->data='The token timeout!';
			return $echoData;
		}
		$echoData->result=true;
		$echoData->data=$user;
		return $echoData;
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
	public function createSubAccount($friendlyName){
		//主帐号
		$accountSid= 'aaf98f8944f35b130144f3b5412b0076';
		//主帐号Token
		$accountToken= 'f708d6bf0d33463bac5313571e91cbe9';
		//应用Id
		$appId='8a48b5514ecd7fa8014edc9c8ade1530';
		//请求地址，格式如下，不需要写https://
		$serverIP='sandboxapp.cloopen.com';
		//请求端口 
		$serverPort='8883';
		//REST版本号
		$softVersion='2013-12-26';
		$this->CI->load->library('CCPRestSDK',array('ServerIP'=>$serverIP,'ServerPort'=>$serverPort,'SoftVersion'=>$softVersion));
		$this->CI->ccprestsdk->setAccount($accountSid,$accountToken);
   		$this->CI->ccprestsdk->setAppId($appId);
   		// 调用云通讯平台的创建子帐号,绑定您的子帐号名称
		//echo "Try to create a subaccount, binding to user $friendlyName <br/>";
	    $result = $this->CI->ccprestsdk->CreateSubAccount($friendlyName);
	    $echoData=new stdClass;
	    if($result == NULL ) {
	        $echoData->result=-1;
			$echoData->data='result error!';
	    }elseif($result->statusCode!=0) {
	        $echoData->result=$result->statusCode;
			$echoData->data=$result->statusMsg;
	    }else {
	        //echo "create SubbAccount success<br/>";
	        // 获取返回信息
	        $subaccount = $result->SubAccount;
	        $echoData->result=0;
	        $data=new stdClass;
			$data->subAccountSid=$subaccount->subAccountSid;
			$data->subToken=$subaccount->subToken;
			$data->dateCreated=$subaccount->dateCreated;
			$data->voipAccount=$subaccount->voipAccount;
			$data->voipPwd=$subaccount->voipPwd;
			$echoData->data=$data;
	    }
		return $echoData;
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