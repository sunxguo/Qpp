<?php 
@session_start();
/**
 * 检查用户登录状态
 * @return boolean 是否已登录
 */
function checkLogin(){
	if(isset($_SESSION['username'])){
		return true;
	} else {
		if (isset($_COOKIE["userid"])) {
			$_SESSION["userid"] = $_COOKIE["userid"];
			$_SESSION["username"] = $_COOKIE["username"];
			$_SESSION["useremail"] = $_COOKIE["useremail"];
			return true;
		}
		return false;
	}
}
function contest_status($start,$end){
	if(strtotime($start)>time()){
		return array("name"=>"wait","name_cn"=>"未开始");
	}
	if(strtotime($end)<time()){
		return array("name"=>"completed","name_cn"=>"已结束");
	}
	return array("name"=>"ing","name_cn"=>"进行中");
}
function load404($title="找不到",$msg="抱歉，找不到该页面"){
	$this->load->view('header',array("title"=>$title));
	$this->load->view('404',array("message"=>$msg));
	$this->load->view('footer');
}
/**
 * 返回错误信息数组
 */
function errorMessage($code, $message){
	return array('code'=>$code,'message'=>$message);
}

/**
 * 生成cookie数组
 * @param string $key 
 * @param string $value
 */
function createCookieArray($key, $value, $expire) {
	$cookie = array(
		'name'   => $key,
		'value'  => $value,
		'expire' => "".$expire,
		'domain' => '',
		'path'   => '/',
		'prefix' => '',
		'secure' => false,
	);
	return $cookie;
}

/**
 * http get
 * @param string $url url to get
 * @return html return
 */
function httpGet($url, $param = array(), $header = array()) {
	$paramString = "?";
	foreach ($param as $key => $value) $paramString = $paramString.$key."=".$value."&";
	if($paramString != "") $paramString[strlen($paramString) - 1] = '';

	if (count($header) == 0) {
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";   
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";   
		$header[] = "Cache-Control: max-age=0";   
		$header[] = "Connection: keep-alive";   
		$header[] = "Keep-Alive: 300";   
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";   
		$header[] = "Accept-Language: en-us,en;q=0.5";   
		$header[] = "Pragma: ";
	}
	
	//$ch = curl_init($url.$paramString);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url.$paramString);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}

/**
 * http post
 * @param string $url url to post
 * @return html return
 */
function httpPost($url, $param = array(), $header = array()) {
	if (count($header) == 0) {
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";   
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";   
		$header[] = "Cache-Control: max-age=0";   
		$header[] = "Connection: keep-alive";   
		$header[] = "Keep-Alive: 300";   
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";   
		$header[] = "Accept-Language: en-us,en;q=0.5";   
		$header[] = "Pragma: ";
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}

/**
 * http patch
 * @param string $url url to patch
 * @return html return
 */
function httpPatch($url, $param = array(), $header = array()) {
	if (false && count($header) == 0) {
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";   
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";   
		$header[] = "Cache-Control: max-age=0";   
		$header[] = "Connection: keep-alive";   
		$header[] = "Keep-Alive: 300";   
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";   
		$header[] = "Accept-Language: en-us,en;q=0.5";   
		$header[] = "Pragma: ";
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}

/**
 * http delete
 * @param string $url url to patch
 * @return html return
 */
function httpDelete($url,  $header = array()) {
	if (count($header) == 0) {
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";   
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";   
		$header[] = "Cache-Control: max-age=0";   
		$header[] = "Connection: keep-alive";   
		$header[] = "Keep-Alive: 300";   
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";   
		$header[] = "Accept-Language: en-us,en;q=0.5";   
		$header[] = "Pragma: ";
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}
function veri_code(){
	//创建图片
	$im = imagecreate($x=130,$y=45 );
	$bg = imagecolorallocate($im,rand(50,200),rand(0,155),rand(0,155)); //第一次对 imagecolorallocate() 的调用会给基于调色板的图像填充背景色
	$fontColor = imageColorAllocate ( $im, 255, 255, 255 );   //字体颜色
	$fontstyle = $_SERVER['DOCUMENT_ROOT'].'/assets/font/msyh.ttf';                   //字体样式，这个可以从c:\windows\Fonts\文件夹下找到，我把它放到和authcode.php文件同一个目录，这里可以替换其他的字体样式
	//产生随机字符
	for($i = 0; $i < 4; $i ++) {
			$randAsciiNumArray         = array (rand(48,57),rand(65,90));
			$randAsciiNum                 = $randAsciiNumArray [rand ( 0, 1 )];
			$randStr                         = chr ( $randAsciiNum );
			imagettftext($im,30,rand(0,20)-rand(0,25),5+$i*30,rand(30,35),$fontColor,$fontstyle,$randStr);
			$authcode                        .= $randStr; 
	}
	$_SESSION['authcode']        = $authcode;//用户和用户输入的验证码做比较
	//干扰线
	for ($i=0;$i<8;$i++){
			$lineColor        = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
			imageline ($im,rand(0,$x),0,rand(0,$x),$y,$lineColor);
	}
	//干扰点
	for ($i=0;$i<250;$i++){
			imagesetpixel($im,rand(0,$x),rand(0,$y),$fontColor);
	}
	ob_clean();
	header ( 'Content-type: image/png' );
	imagepng($im);
}
function mobileCode(){
	//mt_srand((double)microtime() * 1000000);
	$mobileauthcode = mt_rand(100000, 999999);
	unset($_SESSION["mobilecode"]);
	$_SESSION["mobilecode"] = $mobileauthcode;
	return $mobileauthcode;
}
?>