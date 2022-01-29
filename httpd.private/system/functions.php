<?php
include_model('auth');
use authModel as auth;

function include_controller($file){
	include_once(CONTROLLER.$file.'.controller.php');
}

function include_model($file){
	include_once(MODEL.$file.'.model.php');
}

function url($ext){
	return BASEURL.$ext;
}

function createDir($dir){
	if (!file_exists($dir)) {
		mkdir($dir, 0777);
	}
}

function usrDB($usr, $cnd=''){
	$condition = ($cnd == '')? $cnd : ', '.$cnd;
	new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db'.$condition);
}

function char_convert_special($string){
	$string = ltrim(rtrim(preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", "\n\n", $string)));
	$search = array("!", '"', "$", "%", "*", "(", ")", "<", ">", "?", "+", "-", "=", "'"); 
	$replace = array("&#33;", "&#34;", "&#36;", "&#37;", "&#42;", "&#40;", "&#41;", "&#60;", "&#62;", "&#63;", "&#43;", "&#45;", "&#61;", "&apos;");
	$string = str_replace($search, $replace, $string);
	return str_replace("\n", "<br>", $string);
}

function get_extension($file) {
	$p = '.';
	$extension = end(explode(".", $file));
	return $extension ? $extension : false;
}

function truncate_chars($text, $limit, $ellipsis = ' >...') {
	if( strlen($text) > $limit ) {
		$endpos = strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit);
		if($endpos !== FALSE){
			$text = trim(substr($text, 0, $endpos)) . $ellipsis;
		}
	}
		return $text;
}

function random_string($n) { 
$charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	$string = ''; 

	for ($i = 0; $i < $n; $i++) { 
		$index = rand(0, strlen($charset) - 1); 
		$string .= $charset[$index]; 
	} 

	return $string; 
}

function fetchUserDetails(){
	$data = array();
	if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
		$key = str_replace(' ', '', $_SESSION['auth']);
		$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
		if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
			$userDetails = auth::fetchUser($uid);
			foreach($userDetails as $row){
				$data[] = array(   
					'handle' => $row['handle'],
					'username' => $row['username'],
					'img' => '/assets/images/users/'.$row['handle'][0].'/'.$row['handle'][1].'/'.$row['handle'][2].'/'.$row['handle'].'/'
				);
			}
		}
	}
	return $data;
}

$request = parse_url($_SERVER['REQUEST_URI']);
$path = $request["path"];
$result = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $path), '/');