<?php
namespace infrajs\user;
use infrajs\session\Session;
use infrajs\nostore\Nostore;
use infrajs\config\Config;
use infrajs\lang\Lang;
use infrajs\view\View;
use infrajs\load\Load;
use infrajs\template\Template;
use infrajs\mail\Mail;

class User
{
	public static function is($group = false)
	{
		$email = Session::getEmail();
		if (!$email) return false;

		$verify = Session::getVerify();
		if (!$verify) return false;

		$conf = Config::get('user');
		Nostore::on();
		if (!$group) return true;
		
		if (empty($conf[$group])) return false;

		return in_array($email, $conf['user'][$group]);
	}
	public static function isAdmin()
	{
		return self::is('admin');
	}
	public static function get()
	{
		$json = '-user/get.php';
		$user = Load::loadJSON($json);

		return $user;
	}
	/*public static function sentEmail($email, $tpl, $data = array())
	{
		$conf = Config::get('user');
		$data['host'] = View::getHost();
		$data['path'] = View::getPath();
		$data['schema'] = View::getSchema();
		$data['conf'] = $conf;
		$data['email'] = $email;
		$data['time'] = time();

		call_user_func($conf['sentEmail'], $email, $tpl, $data);
	}*/
	public static function getEmail()
	{
		return Session::getEmail();
	}
	public static function checkData($str, $type = 'value')
	{
		switch ($type) {
			case 'radio':
				return !!$str;
			case 'value':
				return $str && strlen($str) > 1;
			case 'password':
				return $str && strlen($str) > 1;
			case 'email':
				return $str && preg_match('/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/', $str);
		}
	}
	public static function mail($email, $mailroot, $data = array())
	{
		if (!$email) return 'Wrong email.';
		if (!$mailroot) return;
		
		//Когда нет указаний в конфиге... ничего такого...
		$ln = Lang::name();
		$tpl = '-user/i18n/'.$ln.'.mail.tpl';
		
		$conf = Config::get('user');
		$data['host'] = View::getHost();
		$data['path'] = View::getPath();
		$data['schema'] = View::getSchema();
		$data['conf'] = $conf;
		$data['email'] = $email;
		$data['time'] = time();

		$subject = Template::parse($tpl, $data, $mailroot.'-subject');
		$body = Template::parse($tpl, $data, $mailroot);

		return Mail::fromAdmin($subject, $email, $body);
	}
	/**
	 * Волшебная функция, которая не пропускает дальше незарегистрированного пользователя 
	 * Исопльзуется в формах где указывается email.
	 * Требуется выполнить регистрацию если указан email уже существующего пользователя
	 * Проводит тихую регистрацию если пользователь не зарегистирован и email Не занят
	 **/
	public static function lang($str = null)
	{
		if (is_null($str)) return Lang::name('user');
		return Lang::str('user',$str);
	}
	public static function checkReg($email, $password = false) 
	{ //Сессия остаётся текущей
		$email = trim(strip_tags($email));
		if (!User::checkData($email, 'email')) return User::lang('You need to provide a valid email');
		$myemail = Session::getEmail();
		if (!$myemail) {//Значит пользователь не зарегистрирован
			$userData = Session::getUser($email);// еще надо проверить есть ли уже такой эмаил
			if ($userData['session_id']) {
				return User::lang('To your email on the website there is a registration, you need to <a href=\'/user/signin\'>login</a>');
			} else {
				Session::setEmail($email);
				if ($password) Session::setPass($password);
				return User::mail($email, 'signup');
			}
		}
	}
}
