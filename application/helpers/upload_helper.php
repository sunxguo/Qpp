<?php
function upload($upload_file_name="files"){
	//文件保存目录路径
	$save_path = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
	//文件保存目录URL
	$save_url = '/uploads/';
	//定义允许上传的文件扩展名
	$ext_arr = array(
		'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
		'flash' => array('swf', 'flv'),
		'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
		'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
	);
	//最大文件大小
	$max_size = 10000000;

	$save_path = realpath($save_path) . '/';

	//PHP上传失败
	if (!empty($_FILES[$upload_file_name]['error'])) {
		switch($_FILES[$upload_file_name]['error']){
			case '1':
				$error = '超过允许的大小。';//php.ini允许的大小
				break;
			case '2':
				$error = '超过表单允许的大小。';
				break;
			case '3':
				$error = '图片只有部分被上传。';
				break;
			case '4':
				$error = '请选择图片。';
				break;
			case '6':
				$error = '找不到临时目录。';
				break;
			case '7':
				$error = '写文件到硬盘出错。';
				break;
			case '8':
				$error = 'File upload stopped by extension。';
				break;
			case '999':
			default:
				$error = '未知错误。';
		}
		return array("code"=>false,"message"=>$error);
	}

	//有上传文件时
	if (empty($_FILES) === false) {
		//原文件名
		$file_name = $_FILES[$upload_file_name]['name'];
		//服务器上临时文件名
		$tmp_name = $_FILES[$upload_file_name]['tmp_name'];
		//文件大小
		$file_size = $_FILES[$upload_file_name]['size'];
		//检查文件名
		if (!$file_name) {
			return array("code"=>false,"message"=>"请选择文件。");
		}
		//检查目录
		if (@is_dir($save_path) === false) {
			return array("code"=>false,"message"=>"上传目录不存在。");
		}
		//检查目录写权限
		if (@is_writable($save_path) === false) {
			return array("code"=>false,"message"=>"上传目录没有写权限。");
		}
		//检查是否已上传
		if (@is_uploaded_file($tmp_name) === false) {
			return array("code"=>false,"message"=>"上传失败。");
		}
		//检查文件大小
		if ($file_size > $max_size) {
			return array("code"=>false,"message"=>"上传文件大小超过限制。");
		}
		//检查目录名
		$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
		if (empty($ext_arr[$dir_name])) {
			return array("code"=>false,"message"=>"目录名不正确。");
		}
		//获得文件扩展名
		$temp_arr = explode(".", $file_name);
		$file_ext = array_pop($temp_arr);
		$file_ext = trim($file_ext);
		$file_ext = strtolower($file_ext);
		//检查扩展名
		if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
			return array("code"=>false,"message"=>"上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
		}
		//创建文件夹
		if ($dir_name !== '') {
			$save_path .= $dir_name . "/";
			$save_url .= $dir_name . "/";
			if (!file_exists($save_path)) {
				mkdir($save_path);
			}
		}
		$ymd = date("Ymd");
		$save_path .= $ymd . "/";
		$save_url .= $ymd . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
		//新文件名
		$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
		//移动文件
		$file_path = $save_path . $new_file_name;
		if (move_uploaded_file($tmp_name, $file_path) === false) {
			return array("code"=>false,"message"=>"上传文件失败。");
		}
		@chmod($file_path, 0644);
		$file_url = $save_url . $new_file_name;
		resizeImage($file_path,5000,5000,$file_path,$file_ext);
		return array("code"=>true,"message"=>$file_url);
	}
}
function resizeImage($src_imagename,$maxwidth,$maxheight,$savename,$file_type)
{
    $im=createimg($src_imagename,$file_type);
	if($im){
		$current_width = imagesx($im);
		$current_height = imagesy($im);
		$resizewidth_tag=false;
		$resizeheight_tag=false;
		$widthratio=1;
		$heightratio=1;
		$ratio =1;
		if(($maxwidth && $current_width > $maxwidth) || ($maxheight && $current_height > $maxheight))
		{
			if($maxwidth && $current_width>$maxwidth)
			{
				$widthratio = $maxwidth/$current_width;
				$resizewidth_tag = true;
			}
	 
			if($maxheight && $current_height>$maxheight)
			{
				$heightratio = $maxheight/$current_height;
				$resizeheight_tag = true;
			}
	 
			if($resizewidth_tag && $resizeheight_tag)
			{
				if($widthratio<$heightratio)
					$ratio = $widthratio;
				else
					$ratio = $heightratio;
			}
	 
			if($resizewidth_tag && !$resizeheight_tag)
				$ratio = $widthratio;
			if($resizeheight_tag && !$resizewidth_tag)
				$ratio = $heightratio;
	 
			$newwidth = $current_width * $ratio;
			$newheight = $current_height * $ratio;
	 
			if(function_exists("imagecopyresampled"))
			{
				$newim = imagecreatetruecolor($newwidth,$newheight);
				   imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
			}
			else
			{
				$newim = imagecreate($newwidth,$newheight);
			   imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
			}
			showimg($newim,$savename,$file_type);
			imagedestroy($newim);
		}
		else
		{
			imagejpeg($im,$savename);
		}
	}
}
function createimg($filename,$filetype){
	$im="";
	switch($filetype){ //取得背景图片的格式
		case "jpg":
			@$im=imagecreatefromjpeg($filename);
			break;
		case "png":
			@$im=imagecreatefrompng($filename);
			break;
		case "gif":
			@$im=imagecreatefromgif($filename);
			break;
		case "bmp":
			@$im=imagecreatefromwbmp($filename);
			break;
		default:
			return false;            //未知的文件格式
	}
	return $im;
}
function showimg($newim,$savename,$filetype){
	switch($filetype){ //取得背景图片的格式
		case "jpg":
			imagejpeg($newim,$savename);
			break;
		case "png":
			imagepng($newim,$savename);
			break;
		case "gif":
			imagegif($newim,$savename);
			break;
		case "bmp":
			imagewbmp($newim,$savename);
			break;
		default:
			imagejpeg($newim,$savename);
			break;
	}
}

?>