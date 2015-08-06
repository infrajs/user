<?php
/**
 * Проверяем что нет ошибок и возвращаются ожидаемые данные User::get...
 */
use itlife\infra\ext\Ans;
use itlife\user\User;

infra_test(true);
$ans=array();


$data=User::get();
if (!$data || sizeof($data) != 4 || !$data['result'] || !$data['id']) {
	return Ans::err($ans, 'User data get error');
}

return Ans::ret($ans, 'Ok');
