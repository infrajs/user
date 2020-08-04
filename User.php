<?php

namespace infrajs\user;

use infrajs\config\Config;
use infrajs\lang\Lang;
use infrajs\view\View;
use infrajs\load\Load;
use infrajs\template\Template;
use infrajs\mail\Mail;
use infrajs\event\Event;
use infrajs\db\Db;
use infrajs\ans\Ans;

Event::$classes['User'] = function (&$user) {
	return $user['user_id'];
};

class User
{
	public static $tokenlen = 10;
	public static $conf = array();
	public static function makeKey($user)
	{
		return md5($user['password'] . date('Y.m'));
	}

	public static function setDateMail($user)
	{
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET datemail = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$user_id]);
	}
	public static function setVerify($user)
	{
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET verify = 1, dateverify = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$user_id]);
	}
	public static function setActive($user)
	{
		if ($user['dateactive'] + 60 > time()) return;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET dateactive = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$user_id]);
	}
	public static function setEmail($user, $email)
	{
		$user['email'] = $email;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET email = ?, datesignup = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$email, $user_id]);
	}
	public static function setToken(&$user)
	{
		$token = User::makeToken();
		$user['token'] = $token;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET token = ?, datetoken = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$token, $user_id]);
	}
	public static function setPassword(&$user, $password)
	{
		$user['password'] = $password;
		$user_id = $user['user_id'];
		$user['password'] = $password;
		$sql = 'UPDATE users
				SET password = ?
				WHERE user_id = ?';
		return Db::exec($sql, [$password, $user_id]);
	}
	public static function mail($user, $lang, $mailroot, $page = false)
	{
		$email = $user['email'];
		$data = $user;
		if ($page) $data['page'] = $page;

		$https = 'http://';
		if (isset($_SERVER['HTTPS'])) $https = 'https://';
		if (isset($_SERVER['REQUEST_SCHEME'])) $https = $_SERVER['REQUEST_SCHEME'] . '://';

		$data['host'] = $_SERVER['HTTP_HOST'];
		$data['path'] = $https . $data['host'];
		$data['conf'] = User::$conf;
		$data['key'] = User::makeKey($user); //Чтобы подтвердить переход по ссылке на почте
		$data['email'] = $email;
		$data['time'] = time();
		$data['token'] = $user['user_id'] . '-' . $user['token'];

		$tpl = '-user/i18n/' . $lang . '.mail.tpl';
		$subject = Template::parse($tpl, $data, $mailroot . '-subject');
		$body = Template::parse($tpl, $data, $mailroot);
		$r = Mail::html($subject, $body, true, $email); //from to
		if ($r) User::setDateMail($user);
		return $r;
	}
	public static function lang($lang, $str)
	{
		return Lang::lang($lang, 'user', $str);
	}

	//Без кода ошибки в сообщении
	public static function err($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$r = explode('.', $code);
		$msg = User::lang($lang, $r[0]);
		$ans['code'] = $code;
		return Ans::err($ans, $msg);
	}
	//С кодом ошибки в сообщении
	public static function fail($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$r = explode('.', $code);
		$msg = User::lang($lang, $r[0]);
		$msg .= '. ' . User::lang($lang, 'Code') . ' ' . $code . '';
		$ans['code'] = $code;

		return Ans::err($ans, $msg);
	}
	//Без кода ошибки в сообщении
	public static function ret($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::ret($ans);
		$r = explode('.', $code);
		$msg = User::lang($lang, $r[0]);
		if ($code) $ans['code'] = $code;
		return Ans::ret($ans, $msg);
	}


	public static function getList() {
		$sql = 'SELECT 
			user_id, password, verify, token, email, 
			UNIX_TIMESTAMP(datecreate) as datecreate,
			UNIX_TIMESTAMP(datesignup) as datesignup,
			UNIX_TIMESTAMP(dateverify) as dateverify,
			UNIX_TIMESTAMP(dateactive) as dateactive,
			UNIX_TIMESTAMP(datetoken) as datetoken,
			UNIX_TIMESTAMP(datemail) as datemail
			FROM users LIMIT 0,1000';
		$list = Db::all($sql);
		return $list;
	}
	public static function getById($user_id)
	{
		$sql = 'SELECT 
			user_id, password, verify, token, email, 
			UNIX_TIMESTAMP(datecreate) as datecreate,
			UNIX_TIMESTAMP(datesignup) as datesignup,
			UNIX_TIMESTAMP(dateverify) as dateverify,
			UNIX_TIMESTAMP(dateactive) as dateactive,
			UNIX_TIMESTAMP(datetoken) as datetoken,
			UNIX_TIMESTAMP(datemail) as datemail
			FROM users where user_id = ?';
		$user = Db::fetch($sql, [$user_id]);
		return $user;
	}
	public static function getByEmail($email)
	{
		$sql = 'SELECT
			user_id, password, verify, token, email, 
			UNIX_TIMESTAMP(datecreate) as datecreate,
			UNIX_TIMESTAMP(datesignup) as datesignup,
			UNIX_TIMESTAMP(dateverify) as dateverify,
			UNIX_TIMESTAMP(dateactive) as dateactive,
			UNIX_TIMESTAMP(datetoken) as datetoken,
			UNIX_TIMESTAMP(datemail) as datemail
			FROM users where email = ?';
		$user = Db::fetch($sql, [$email]);
		return $user;
	}
	
	
	public static function fromToken($token)
	{
		if ($token == '') return [];
		$r = explode('-', $token);
		if (sizeof($r) != 2) return [];
		$user_id = $r[0];
		$token = $r[1];
		if (!is_numeric($user_id)) return [];
		$user = User::getById($user_id);
		if (!$user) return [];
		if ($user['token'] !== $token) return [];
		User::setActive($user);
		return $user;
	}
	public static function makeToken()
	{
		$token = md5(time() . rand());
		$token = substr($token, 0, User::$tokenlen);
		return $token;
	}
	public static function makePassword()
	{
		$token = md5(time() . rand());
		$token = substr($token, 0, 6);
		return $token;
	}
	public static function create($email = null, $password = null)
	{
		if (!$password) $password = User::makePassword();
		$token = User::makeToken();

		if ($email) $sql = 'INSERT INTO users (email, password, token, datesignup, datetoken, datecreate) VALUES(?,?,?,now(),now(),now())';
		else $sql = 'INSERT INTO users (password, token, datetoken, datecreate) VALUES(?,?,now(),now())';
		$user_id = Db::lastId($sql, [$email, $password, $token]);
		if (!$user_id) return false;
		return User::getById($user_id);
	}
	public static function checkPassword($str)
	{
		return $str && strlen($str) > 2;
	}
	public static function mergefromto(&$user, $fuser)
	{
		$relations = User::$conf['relations']; //Массив с таблицами в которых нужно изменить user_id
		foreach ($relations as $plugin) {
			Config::get($plugin);
			$user['to'] = $fuser;
			Event::fire('User.merge', $user);

			//$sql = 'UPDATE ' . $tbl . ' SET user_id = ? WHERE user_id = ?';
			//Db::exec($sql, [$fuser['user_id'], $user['user_id']]);
			//myorders (user_id, active, order_id) [1-1]-1, [2-1]-2 = ([1-1]-1, [1-1]-2)
			//Может быть только одна активная заявка
		}
		$user = [];
		$sql = 'DELETE from users where user_id = ?';
		Db::exec($sql, [$user['user_id']]);
	}












	/*
		$user = User::getByEmail($email)
		if (!$user) {
			$user = User::create($email)
			User::mail($user, $lang, 'welcome')
		} else {
			User::mail($email, 'userdata', $data);
			return User.err($ans, $lang, '...')
			return "<p>
				На сайте есть регистрация на указанный адрес. Отправлено письмо со ссылкой для быстрого входа. 
				Нужно подтвердить, что это Вы. 
				</p>
				<p>
					Указать логин и пароль вручную можно на странице <a href='/user/signin?back=ref'>авторизации</a>.
				</p>";
				<p>После авторизации вы сможете продолжить работу с заявкой</p>"
		}			
	*/

	// public static function checkReg($email, $page = false)
	// { //Сессия остаётся текущей
	// 	$email = trim(strip_tags($email));
	// 	if (!User::checkData($email, 'email')) return User::lang('You need to provide a valid email');
	// 	$myemail = Session::getEmail();
	// 	if (!$myemail) { //Значит пользователь не зарегистрирован
	// 		$user = Session::getUser($email); // еще надо проверить есть ли уже такой эмаил
	// 		if ($user['session_id']) {
	// 			$data = array();
	// 			if ($page) $data['page'] = $page;
	// 			User::sentEmail($email, 'userdata', $data);
	// 			return "<p>На <b>" . $email . "</b> отправлено письмо со ссылкой для быстрого входа. На сайте есть регистрация на адрес <b>" . $email . "</b>. Вам нужно подтвердить что это Вы. </p><p>Указать логин и пароль вручную можно на странице <a href='/user/signin?back=ref'>авторизации</a>.</p>";
	// 			//return User::lang('To your email on the website there is a registration, you need to <a href=\'/user/signin?back=ref\'>login</a>');
	// 		} else {
	// 			Session::setEmail($email);
	// 			$user = Session::getUser($email);
	// 			$password = $user['password'];

	// 			$data = array();
	// 			//$data['key'] = md5($password.date('Y.m'));
	// 			//$msg = User::sentEmail($email, 'signup', $data);
	// 			$msg = User::sentEmail($email, 'welcome', $data);
	// 			if ($msg) {
	// 				//Письмо не отправлено, но нам то что... у человека появилась регистрация. И если что он пароль восстановит
	// 			}
	// 			return false;
	// 		}
	// 	}
	// }
	// public static function isAdmin()
	// {
	// 	return self::is('admin');
	// }
	// public static function get()
	// {
	// 	$json = '-user/get.php';
	// 	$user = Load::loadJSON($json);

	// 	return $user;
	// }

	// public static function getEmail()
	// {
	// 	return Session::getEmail();
	// }
	// public static function checkData($str, $type = 'value')
	// {
	// 	switch ($type) {
	// 		case 'radio':
	// 			return !!$str;
	// 		case 'value':
	// 			return $str && strlen($str) > 1;
	// 	}
	// }
}
