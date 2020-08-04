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
	}
	
	Template::$scope['User'] = array();
	Template::$scope['User']['token'] = function () {
		header('Cache-Control: no-store'); 
		return View::getCOOKIE('token');
	};
	Template::$scope['User']['lang'] = function ($stren = null) {
		//Для контроллера функция
		$lang = Lang::name('user');
		if (is_null($stren)) return $lang;
		return User::lang($lang, $stren);
	};
});
