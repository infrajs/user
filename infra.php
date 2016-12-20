<?php

use infrajs\event\Event;
use infrajs\user\User;
use infrajs\template\Template;

Event::one('Controller.oninit', function () {
	Template::$scope['User'] = array();
	Template::$scope['User']['lang'] = function ($str) {
		return User::lang($str);
	};
});
