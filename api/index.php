<?php

//use infrajs\nostore\Nostore;
//use infrajs\config\Config;
use infrajs\user\User;
//use infrajs\view\View;
use infrajs\load\Load;
use infrajs\ans\Ans;
use infrajs\rest\Rest;
use infrajs\mail\Mail;

//Вложенный скрипт запрещает кэш по всей иерархии
header('Cache-Control: no-store');
// if (!Load::isphp()) { //Заголовок Content-Type важен только для текущего уровня, а на другом может быть что-то другое
// 	// Выставляет ans при отправке ответа
// 	header('Content-Type: application/json');
// }

$ans = [];

$lang = Ans::REQ('lang', User::$conf['lang']['list'], User::$conf['lang']['def']);

//Серверу REST нельзя работать с COOKIE
//$token = Ans::VAL('token','string','');
$token = Ans::REQ('token', 'string', '');
$user = User::fromToken($token);

//$auth = !empty($user['email']); //Есть регистрация и есть аккаунт
//$guest = $user && empty($user['email']); //Нет регистрация и есть аккаунт


$submit = ($_SERVER['REQUEST_METHOD'] === 'POST' || Ans::GET('submit', 'bool'));

//$back = Ans::GET('back', 'string', '/user');
//$ans['go'] = $back;

$admin = $user ? in_array($user['email'], User::$conf['admin']) : false;

// $ans['lang'] = $lang;
// $ans['token'] = $token;
// $ans['user'] = $user;
// $ans['submit'] = $submit;



return Rest::get(function () use ($ans, $lang, $user, $submit, $admin) {
	return User::fail($ans, $lang, "U016");
}, 'whoami', function () use ($ans, $lang, $user, $submit, $admin) {
	unset($user['password']);
	unset($user['token']);
	unset($user['datecreate']);
	unset($user['datesignup']);
	unset($user['dateverify']);
	unset($user['datetoken']);
	unset($user['dateactive']);
	if (!empty($user['email']))	$user['admin'] = in_array($user['email'], User::$conf['admin']);
	$ans['user'] = $user;
	return Ans::ret($ans);
}, 'create', function () use ($ans, $lang, $user, $submit, $admin) {
	if (!$submit) return User::fail($ans, $lang, 'U001.1');
	if (!$admin) return User::fail($ans, $lang, 'U015');

	$email = Ans::REQ('email');
	if ($email) {
		if (!Mail::check($email)) return User::fail($ans, $lang, 'U006.2');
		if ($user && $user['email'] == $email) return User::err($ans, $lang, 'U005.2');
		$fuser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
		if ($fuser) return User::fail($ans, $lang, 'U008.2');
	}

	$fuser = User::create($email); //user_id, token
	if (!$fuser) return User::fail($ans, $lang, 'U014.2');
	$ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

	return User::ret($ans, $lang, 'U017');
}, 'signin', function () use ($ans, $lang, $user, $submit, $admin) {
	if (!empty($user['email'])) return User::err($ans, $lang, 'U005.3');
	if (!$submit) return Ans::ret($ans);

	$email = Ans::REQ('email');
	if (!Mail::check($email)) return User::err($ans, $lang, 'U006.3');

	$fuser = User::getByEmail($email);
	if (!$fuser) return User::err($ans, $lang, 'U019.1');

	$password = Ans::REQ('password');
	if (!$password || $fuser['password'] !== $password) return User::err($ans, $lang, 'U018');

	//При авторизации гостя сливаем
	if ($user) User::mergefromto($user, $fuser);

	$ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
	//depricated Авторизировать должен контроллер в REST-путях COOKIE.token. REST не работает с COOKKIE
	//signin возвращает token а контроллер-клиента его устанавливает
	//View::setCOOKIE('token', $ans['token']);

	return User::ret($ans, $lang, 'U020');
}, 'signup', function () use ($ans, $lang, $user, $submit, $admin) {
	if (!empty($user['email'])) return User::err($ans, $lang, 'U005.1');
	if (!$submit) return Ans::ret($ans);


	$email = Ans::REQ('email');
	if (!Mail::check($email)) return User::err($ans, $lang, 'U006.1');

	$olduser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
	if ($olduser) return User::err($ans, $lang, 'U008');

	$password = Ans::REQ('password');
	if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.1');

	$repeatpassword = Ans::REQ('repeatpassword');
	if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.1');

	$terms = Ans::REQ('terms');
	if (!$terms) return User::err($ans, $lang, 'U011');

	//При авторизации гостя сливаем
	if ($user && empty($user['email'])) {
		User::setPassword($user, $password);
		User::setEmail($user, $email);
		User::setToken($user);
		$fuser = $user;
	} else {
		$fuser = User::create($email, $password); //user_id, token
		if (!$fuser) return User::fail($ans, $lang, 'U014');
	}

	$ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
	//View::setCOOKIE('token', $ans['token']);

	$r = User::mail($fuser, $lang, 'welcome');
	if (!$r) return User::ret($ans, $lang, 'U012.1');

	return User::ret($ans, $lang, 'U013');
}, 'logout', function () use ($ans, $lang, $user, $submit, $admin) {
	//Заглушка, бесполезная функция
	//if (!empty($user['email'])) 
	//Гость тоже может выйти и забыть корзину
	if (!$user) return User::err($ans, $lang, 'U021.1');
	//if (!$submit) return User::fail($ans, $lang, 'U001.4');
	if (!$submit) return Ans::ret($ans);

	$ans['token'] = '';
	return User::ret($ans, $lang, 'U022');
}, 'user', function () use ($ans, $lang, $user, $submit, $admin) {

	$fuser = false;
	$email = Ans::REQ('email');
	if ($email) {
		if (!Mail::check($email)) return User::err($ans, $lang, 'U006.5');
		if ((!$user || $user['email'] !== $email) && !$admin) return User::err($ans, $lang, 'U023.5');
		$fuser = User::getByEmail($email);
		if (!$fuser) return User::err($ans, $lang, 'U025.1');
	}

	$user_id = Ans::REQ('user_id', 'int');
	if ($user_id) {
		if ((!$user || $user['user_id'] !== $user_id) && !$admin) return User::err($ans, $lang, 'U023.6');
		$fuser = User::getById($user_id);
		if (!$fuser) return User::err($ans, $lang, 'U025.2');
	}

	if (!$fuser) return User::err($ans, $lang, 'U024');
	unset($fuser['password']);
	unset($fuser['token']);
	$fuser['admin'] = in_array($fuser['email'], User::$conf['admin']);
	$ans['user'] = $fuser;
	return Ans::ret($ans);
}, 'remind', function () use ($ans, $lang, $user, $submit, $admin) {
	//Гость с корзиной может вспоминать пароль и при этом сохранить корзину. Но если ты в аккаунте с email тогда объединения не будет, при восстановлении прароля ты итак вернёшься к своей корзине
	if (!empty($user['email'])) return User::err($ans, $lang, 'U005.4');

	if (!$submit) return Ans::ret($ans);

	if (isset($user['datemail']) && $user['datemail'] > time() - (60 * 5)) return User::err($ans, $lang, 'U033.1');

	$email = Ans::REQ('email');
	if (!Mail::check($email)) return User::err($ans, $lang, 'U006.4');


	$fuser = User::getByEmail($email);
	if (!$fuser) return User::err($ans, $lang, 'U019.2');

	$r = User::mail($fuser, $lang, 'remind');
	if (!$r) return User::ret($ans, $lang, 'U012.2');
	
	return User::ret($ans, $lang, 'U026.1');
}, 'remindkey', function () use ($ans, $lang, $user, $submit, $admin) {
	if (!empty($user['email'])) return User::err($ans, $lang, 'U005.5');

	$email = Ans::REQ('email');
	if (!Mail::check($email)) return User::err($ans, $lang, 'U006.5');

	$fuser = User::getByEmail($email);
	if (!$fuser) return User::err($ans, $lang, 'U019.3');

	$key = Ans::REQ('key');
	if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.1');

	if (!$fuser['verify']) User::setVerify($fuser);

	if (!$submit) return Ans::ret($ans);


	$password = Ans::REQ('password');
	if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.2');

	$repeatpassword = Ans::REQ('repeatpassword');
	if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.2');

	User::setPassword($fuser, $password);
	User::setToken($fuser); //При восстановлении пароля, token сбрасывается

	if ($user) User::mergefromto($user, $fuser); //Нужно данные из гостевого перенести в аккаунт где установлен счейчас новый пароль

	$ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

	$r = User::mail($fuser, $lang, 'newpass');

	//$ans['go'] = '/user';
	//$ans['popup'] = true;

	return User::ret($ans, $lang, 'U028');
}, 'change', function () use ($ans, $lang, $user, $submit, $admin) {
	if (empty($user['email'])) return User::err($ans, $lang, 'U021.3');
	if (!$submit) return Ans::ret($ans);

	$oldpassword = Ans::REQ('oldpassword');

	if ($user['password'] != $oldpassword) return User::err($ans, $lang, 'U029');

	$newpassword = Ans::REQ('newpassword');
	if (!User::checkPassword($newpassword)) return User::err($ans, $lang, 'U009.3');

	$repeatnewpassword = Ans::REQ('repeatnewpassword');
	if ($newpassword != $repeatnewpassword) return User::err($ans, $lang, 'U010.1');

	User::setPassword($user, $newpassword);
	User::setToken($user);

	$ans['token'] = $user['user_id'] . '-' . $user['token'];

	User::mail($user, $lang, 'newpass');

	return User::ret($ans, $lang, 'U030');
}, 'confirm', function () use ($ans, $lang, $user, $submit, $admin) {
	//if (!$submit) return User::fail($ans, $lang, 'U001.2');
	if (empty($user['email'])) return User::err($ans, $lang, 'U021.2');
	if (!empty($user['verify'])) return User::ret($ans, $lang, 'U031.1');
	$ans['datemail'] = $user['datemail'];
	if (!$submit) return Ans::ret($ans);

	if (isset($user['datemail']) && $user['datemail'] > time() - (60 * 5)) return User::err($ans, $lang, 'U033.1');

	$r = User::mail($user, $lang, 'confirm');
	if (!$r) return User::err($ans, $lang, 'U012.3');

	return User::ret($ans, $lang, 'U026.2');
}, 'list', function () use ($ans, $lang, $user, $submit, $admin) {
	if (!$admin) return User::err($ans, $lang, 'U023.5');
	$ans['list'] = User::getList();
	return Ans::ret($ans);
}, 'confirmkey', function () use ($ans, $lang, $user, $submit, $admin) {
	$email = Ans::REQ('email');
	if (!Mail::check($email)) return User::err($ans, $lang, 'U006.6');

	$fuser = User::getByEmail($email);
	if (!$fuser) return User::fail($ans, $lang, 'U025.3');

	$key = Ans::REQ('key');
	if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.2');

	if (!empty($fuser['verify'])) return User::ret($ans, $lang, 'U031.2');

	User::setVerify($fuser);

	return User::ret($ans, $lang, 'U032');
});
