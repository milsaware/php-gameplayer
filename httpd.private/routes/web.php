<?php
use routesController as route;
$route = preg_replace('#[^a-z0-9_]#', '', $_GET['route']);

if($route === 'home'){
	route::get('home', 'app@index');
}
elseif($route === 'game'){
	route::get('game', 'app@game');
}
elseif($route === 'submit_form'){
	route::get('submit_form', 'form@index');
}
elseif($route === 'auth'){
	route::get('auth', 'auth@index');
}
elseif($route === 'profile'){
	route::get('profile', 'profile@index');
}
else{
	$userDetails = fetchUserDetails();
	echo $userDetails[0]['img'];
	if(isset($userDetails[0]['handle'])){
		$data['handle'] = $userDetails[0]['handle'];
		$img = $userDetails[0]['img'];
		$data['meta']['img'] = $img;
	}
	$data['meta']['title'] = SITENAME.' - page not found';
	$data['meta']['description'] = '';
		
	$data['content'] = 'Page not found';

	$data['copyright'] = '&#169; '.date('Y').' '.SITENAME;
	route::error($data);
}
