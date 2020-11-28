<?php

namespace infrajs\user;

use infrajs\config\Config;
use infrajs\lang\Lang;
use infrajs\view\View;
use infrajs\load\Load;
use infrajs\template\Template;
use infrajs\mail\Mail;
use infrajs\access\Access;
use infrajs\event\Event;
use infrajs\db\Db;
use infrajs\ans\Ans;

use infrajs\lang\LangAns;
use infrajs\cache\CacheOnce;

Event::$classes['User'] = function (&$user) {
	return $user['user_id'];
};

class User
{
	public static $tokenlen = 10;
	public static $conf = array();

	public static $name = 'user';
	use LangAns;
	use CacheOnce;
	use UserMail;
	public static function mailbefore(&$data)
	{
		$data['key'] = User::makeKey($data); //Чтобы подтвердить переход по ссылке на почте
	}
	public static function mailafter($data, $r)
	{
		if ($r) User::setDateMail($data);
	}



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
		$r = Db::exec($sql, [$user_id]) !== false;
		return $r;
	}
	
	public static function setEnv(&$fuser, $timezone, $lang, $city_id)
	{
		static::$once = [];
		$fuser['timezone'] = $timezone;
		$fuser['lang'] = $lang;
		$fuser['city_id'] = $city_id;

		$user_id = $fuser['user_id'];
		$sql = 'UPDATE users
				SET lang = :lang, timezone = :timezone, city_id = :city_id
				WHERE user_id = :user_id';
		$r = Db::exec($sql, [
			':user_id' => $user_id,
			':lang' => $lang,
			':timezone' => $timezone,
			':city_id' => $city_id
		]) !== false;
		return $r;
	}
	// public static function setLang(&$fuser, $lang)
	// {
	// 	static::$once = [];
	// 	$fuser['lang'] = $lang;
	// 	$user_id = $fuser['user_id'];
	// 	$sql = 'UPDATE users
	// 			SET lang = :lang
	// 			WHERE user_id = :user_id';
	// 	$r = Db::exec($sql, [
	// 		':user_id' => $user_id,
	// 		':lang' => $lang
	// 	]) !== false;
	// 	return $r;
	// }
	public static function setCity(&$fuser, $city_id)
	{
		static::$once = [];
		$fuser['city_id'] = $city_id;
		$user_id = $fuser['user_id'];
		$sql = 'UPDATE users
				SET city_id = :city_id
				WHERE user_id = :user_id';
		$r = Db::exec($sql, [
			':user_id' => $user_id,
			':city_id' => $city_id
		]) !== false;
		return $r;
	}
	// public static function setTimezone(&$fuser, $timezone)
	// {
	// 	static::$once = [];
	// 	$fuser['timezone'] = $timezone;
	// 	$user_id = $fuser['user_id'];
	// 	$sql = 'UPDATE users
	// 			SET timezone = :timezone
	// 			WHERE user_id = :user_id';
	// 	$r = Db::exec($sql, [
	// 		':user_id' => $user_id,
	// 		':timezone' => $timezone
	// 	]) !== false;
	// 	return $r;
	// }

	public static function setVerify(&$user)
	{
		static::$once = [];
		$user_id = $user['user_id'];
		$user['verify'] = 1;
		$sql = 'UPDATE users
				SET verify = 1, dateverify = now()
				WHERE user_id = :user_id';
		$r = Db::exec($sql, [
			':user_id' => $user_id
		]) != false;
		return $r;
	}
	public static function setActive($user)
	{
		if ($user['dateactive'] && $user['dateactive'] + 60 > time()) return;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET dateactive = now()
				WHERE user_id = :user_id';
		$r = Db::exec($sql, [
			':user_id' => $user_id
		]) != false;
		return $r;
	}
	public static function setEmail(&$user, $email)
	{
		static::$once = [];
		$user['email'] = $email;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET email = ?, datesignup = now()
				WHERE user_id = ?';
		$r = Db::exec($sql, [$email, $user_id]) !== false;
		return $r;
	}
	public static function setToken(&$user)
	{
		static::$once = [];
		$token = User::makeToken();
		$user['token'] = $token;
		$user_id = $user['user_id'];
		$sql = 'UPDATE users
				SET token = ?, datetoken = now()
				WHERE user_id = ?';
		return Db::exec($sql, [$token, $user_id]) !== false;
	}
	public static function setPassword(&$user, $password)
	{
		static::$once = [];
		$user['password'] = $password;
		$user_id = $user['user_id'];
		$user['password'] = $password;
		$sql = 'UPDATE users
				SET password = ?
				WHERE user_id = ?';
		return Db::exec($sql, [$password, $user_id]) !== false;
	}


	public static function getList($lang)
	{
		return static::once('getList', $lang, function ($lang) {
			$name = $lang == 'ru' ? 'CityName' : 'EngName';
			$sql = "SELECT 
			user_id, password, verify, token, email, c.$name as city,
			UNIX_TIMESTAMP(datecreate) as datecreate,
			UNIX_TIMESTAMP(datesignup) as datesignup,
			UNIX_TIMESTAMP(dateverify) as dateverify,
			UNIX_TIMESTAMP(dateactive) as dateactive,
			UNIX_TIMESTAMP(datetoken) as datetoken,
			UNIX_TIMESTAMP(datemail) as datemail
			FROM users u
			LEFT JOIN city_cities c on c.city_id = u.city_id
			WHERE email is not null
			order by dateactive DESC
			LIMIT 0,1000";
			$list = Db::all($sql);
			return $list;
		});
	}
	public static function getById($user_id)
	{
		return static::once('getById', $user_id, function ($user_id) {

			$sql = 'SELECT 
				user_id, password, verify, token, email, timezone, lang, city_id,
				UNIX_TIMESTAMP(datecreate) as datecreate,
				UNIX_TIMESTAMP(datesignup) as datesignup,
				UNIX_TIMESTAMP(dateverify) as dateverify,
				UNIX_TIMESTAMP(dateactive) as dateactive,
				UNIX_TIMESTAMP(datetoken) as datetoken,
				UNIX_TIMESTAMP(datemail) as datemail
				FROM users where user_id = :user_id';
			$user = Db::fetch($sql, [
				':user_id' => $user_id
			]);
			if ($user) $user['admin'] = (Access::isDebug() || $user['verify']) && in_array($user['email'], User::$conf['admin']);

			return $user;
		});
	}
	public static function getByEmail($email)
	{
		return User::once('getByEmail', $email, function ($email) {
			$user_id = Db::col('SELECT user_id FROM users WHERE email = :email', [
				':email' => $email
			]);
			if (!$user_id) return false;
			return User::getById($user_id);
		});
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
	public static function create($lang, $city_id, $timezone, $email = null, $password = null)
	{
		//timezone, lang, city - pure функция должна получать эти значения параметрами
		if (!$password) $password = User::makePassword();
		$token = User::makeToken();

		if ($email) $sql = 'INSERT INTO users (lang, city_id, timezone, email, password, token, datesignup, datetoken, datecreate) 
			VALUES(:lang, :city_id, :timezone, :email,:password,:token,now(),now(),now())';
		else $sql = 'INSERT INTO users (lang, city_id, timezone, email, password, token, datetoken, datecreate) 
			VALUES(:lang, :city_id, :timezone, :email,:password,:token,now(),now())';

		$user_id = Db::lastId($sql, [
			':lang' => $lang,
			':city_id' => $city_id,
			':timezone' => $timezone,
			':email' => $email,
			':password' => $password,
			':token' => $token
		]);
		if (!$user_id) return false;
		return User::getById($user_id);
	}
	public static function checkPassword($str)
	{
		return $str && strlen($str) > 2;
	}
	public static function mergefromto(&$user, $fuser)
	{	
		if ($user['user_id'] == $fuser['user_id']) return true;
		$user['to'] = $fuser;
		Event::fire('User.merge', $user);

		$sql = 'DELETE from users where user_id = ?';
		Db::exec($sql, [$user['user_id']]);
		$user = [];
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
