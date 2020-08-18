<?php

use infrajs\event\Event;
use infrajs\lang\Lang;
use infrajs\user\User;
use infrajs\template\Template;
use infrajs\view\View;

Event::one('Controller.oninit', function () {
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		$user = User::fromToken($token);
		if ($user) {
			View::setCOOKIE('token', $token);
		}
	} else {
		$token = View::getCOOKIE('token');
		$user = User::fromToken($token);
	}
	
	Template::$scope['User'] = array();
	Template::$scope['User']['token'] = function () use ($token) {
		header('Cache-Control: no-store'); 
		return $token;
	};
	// Template::$scope['User']['email'] = function () use ($user){
	// 	header('Cache-Control: no-store'); 
	// 	if (!$user) return;
	// 	return $user['email'];
	// };
	Template::$scope['User']['lang'] = function ($stren = null) {
		//Для контроллера функция
		$lang = Lang::name('user');
		if (is_null($stren)) return $lang;
		return User::lang($lang, $stren);
	};
});
