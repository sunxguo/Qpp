<?php 
	/**
	 * 客户端代码
	 */
	 
	error_reporting(0);
	//确保在连接客户端时不会超时
	set_time_limit(0);
	//设置IP和端口号
	$address = "localhost";
	$port = 1935;
	echo iconv("UTF-8","GB2312"," TCP/IP Connection \n");
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false)
	{
		die;
	}
	else
	{
		echo "OK";
	}
	echo iconv("UTF-8","GB2312","试图连接 ");
	if (socket_connect($socket, $address, $port) == false)
	{
		$error = socket_strerror(socket_last_error());
		echo "socket_connect() failed.\n","Reason: {$error} \n";
		die;
	}
	else
	{
		echo iconv("UTF-8","GB2312","连接OK\n");
	}
	$in   = "Hello World\r\n";
	if (socket_write($socket, $in, strlen($in)) === false)
	{
		echo "socket_write() failed: reason: " . socket_strerror(socket_last_error()) ."\n";
		die;
	}
	else
	{
		echo iconv("UTF-8","GB2312","发送到服务器信息成功！\n","发送的内容为: $in  \n");
	}
	$out  = "";
	while ($out = socket_read($socket, 8192))
	{
		echo iconv("UTF-8","GB2312","接受的内容为: ".$out."\n");
	}
	echo iconv("UTF-8","GB2312","关闭SOCKET…\n");
	socket_close($socket);
	echo iconv("UTF-8","GB2312","关闭OK\n");
?>