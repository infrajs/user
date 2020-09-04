<?php

use infrajs\event\Event;
use infrajs\lang\Lang;
use infrajs\user\User;
use infrajs\ans\Ans;
use infrajs\template\Template;
use infrajs\view\View;
use infrajs\env\Env;

if (isset($_GET['token'])) {
	$token = $_GET['token'];
	$user = User::fromToken($token);
	//Если менеджер работает за заказом, то время в отправляемом письме клиенту должно быть временем клиента, а не временем менеджера
	if (!empty($user['timezone'])) {
		@date_default_timezone_set($user['timezone']);
	}
}
Event::one('Controller.oninit', function () {
	$user = null;
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		$user = User::fromToken($token);
		if ($user) {
			View::setCOOKIE('token', $token);
		} else {
			$token = '';
		}
	} else {
		$token = View::getCOOKIE('token');
	}
	if (Ans::REQ('-env')) {
		$user = User::fromToken($token);
	}
	if ($user) {
		$city_id = Env::get('city_id');
		$lang = Env::get('lang');
		if ($user['city_id'] != $city_id || $user['lang'] != $lang) {
			$timezone = $user['timezone'];
			User::setEnv($user, $timezone, $lang, $city_id);
		}

	}
	
	Template::$scope['User'] = array();
	Template::$scope['User']['token'] = function () use ($token) {
		header('Cache-Control: no-store'); 
		return $token;
	};
	Template::$scope['User']['lang'] = function ($stren = null) {
		//Для контроллера функция
		$lang = Lang::name('user'); //Без контроллера нельзя так обращатьтся, потому что ответ зависиот от окружения
		if (is_null($stren)) return $lang;
		return User::lang($lang, $stren);
	};
});
