<?php

//use infrajs\nostore\Nostore;
//use infrajs\config\Config;
use infrajs\user\User;
//use infrajs\view\View;
use infrajs\load\Load;
use infrajs\ans\Ans;
use akiyatkin\city\City;
use infrajs\rest\Rest;
use infrajs\mail\Mail;

if (!isset($meta['actions'][$action])) return User::fail($ans, $lang, 'U016.h'.__LINE__);
$handlers = $meta['actions'][$action]['handlers'] ?? [];


if (!empty($handlers['post'])) {
	if (!$submit) return User::fail($ans, $lang, 'U001.h'.__LINE__);
}
if (!empty($handlers['timezone'])) {
    $timezone = Ans::REQ('timezone');//Intl.DateTimeFormat().resolvedOptions().timeZone
    if (!$timezone) return User::fail($ans, $lang, 'U036.h'.__LINE__);
}
if (!empty($handlers['city'])) {
    $city_id = Ans::REQ('city_id', 'int');
	if (!$city_id) return User::fail($ans, $lang, 'U035.h'.__LINE__);
	$city = City::getById($city_id, $lang);
	if (!$city) return User::fail($ans, $lang, 'U035.h'.__LINE__);
}

if (!empty($handlers['admin'])) {
	if (empty($user['admin'])) return User::fail($ans, $lang, 'U015.h'.__LINE__);
}

if (!empty($handlers['fuser'])) {
	$user_id = Ans::REQ('user_id', 'int', null);
	$email = Ans::REQ('email','string',null);
	if (!is_null($user_id)) {
		$fuser = User::getById($user_id);
	} else if (!is_null($email)) {
		if (!Mail::check($email)) return User::err($ans, $lang, 'U006.h'.__LINE__);
		$fuser = User::getByEmail($email);
	}
	// if (is_null($user_id) && is_null($email)) {
	// 	return User::fail($ans, $lang, 'U038.h'.__LINE__);
	// }
	if (!$fuser) $fuser = $user;
    if (!$fuser)  return User::fail($ans, $lang, 'U025.h'.__LINE__);

	if ($fuser) {
		//Можно работать только с данными своего пользователя, если ты не админ конечно
		if (empty($user['admin']) && (!$user || $fuser['user_id'] !== $user['user_id'])) return User::err($ans, $lang, 'U015.h'.__LINE__);
	}
}
