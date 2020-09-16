<?php

use infrajs\event\Event;
use infrajs\lang\Lang;
use infrajs\user\User;
use infrajs\ans\Ans;
use infrajs\template\Template;
use infrajs\view\View;
use infrajs\env\Env;

//Серверная часть. token может быть только в GET
if (isset($_GET['token'])) {
	$token = $_GET['token'];
	$user = User::fromToken($token);
	//TODO Если менеджер работает за заказом, то время в отправляемом письме клиенту должно быть временем клиента, а не временем менеджера
	if (!empty($user['timezone'])) {
		@date_default_timezone_set($user['timezone']);
	}
}

//Клиентская часть. token может быть в Cookies
Event::one('Controller.oninit', function () {
	
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		$user = User::fromToken($token);
		if ($user) {
			$cookietoken = View::getCOOKIE('token');
			if (!$cookietoken) $cookietoken = View::getCOOKIE('infra_session_id').'-'.View::getCOOKIE('infra_session_pass');
			if ($cookietoken) {
				$old_user = User::fromToken($cookietoken);
				if ($old_user && empty($old_user['email'])) { //Можно мёрджить
					//Если есть email значит были письма и мёрджить уже нельзя
					User::mergefromto($old_user, $user);
				}
			}
			View::setCOOKIE('token', $token);
			View::setCOOKIE('infra_session_pass');
			View::setCOOKIE('infra_session_id');
		} else {
			$token = View::getCOOKIE('token');
			if (!$token) $token = View::getCOOKIE('infra_session_id').'-'.View::getCOOKIE('infra_session_pass');
			//Если кривой token может не надо выходить?
			//$token = '';
			//View::setCOOKIE('token');
		}
	} else {
		$token = View::getCOOKIE('token');
		if (!$token) $token = View::getCOOKIE('infra_session_id').'-'.View::getCOOKIE('infra_session_pass');
		$user = null;
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
		//header('Cache-Control: no-store'); 
		//Используется в расчёте адреса, но адрес может не быть активирован, по независящим от token причинам и кэш нельзя блокировать на этом основании
		return $token;
	};
	Template::$scope['User']['lang'] = function ($stren = null) {
		//Для контроллера функция
		$lang = Lang::name('user'); //Без контроллера нельзя так обращатьтся, потому что ответ зависиот от окружения
		if (is_null($stren)) return $lang;
		return User::lang($lang, $stren);
	};
});