<?php
namespace infrajs\user;
use infrajs\session\Session;
use infrajs\nostore\Nostore;
use infrajs\config\Config;
use infrajs\view\View;
use infrajs\template\Template;
use infrajs\mail\Mail;

class User
{
	public static function is($group = false)
	{
		$email = Session::getEmail();
		if (!$email) {
			return false;
		}
		$verify = Session::getVerify();
		if (!$verify) {
			return false;
		}
		$conf = Config::get('user');
		Nostore::on();
		if (!$group) {
			return true;
		}
		if (empty($conf[$group])) {
			return false;
		}
		return in_array($email, $conf['user'][$group]);
	}
	public static function isAdmin()
	{
		return self::is('admin');
	}
	public static function get()
	{
		$json = '-user/get.php';
		$user = infra_loadJSON($json);

		return $user;
	}
	public static function sentEmail($email, $tpl, $data = array())
	{
		$conf = Config::get('user');
		$data['host'] = View::getHost();
		$data['path'] = View::getPath();
		$data['schema'] = View::getSchema();
		$data['conf'] = $conf;
		call_user_func($conf['sentEmail'], $email, $tpl, $data);
	}
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
		if (!$email) {
			return 'Wrong email.';
		}
		if (!$mailroot) {
			return;
		}//Когда нет указаний в конфиге... ничего такого...
		$tpl = '-user/user.mail.tpl';

		$data['host'] = View::getHost();
		$data['path'] = View::getRoot();
		$data['email'] = $email;
		$data['time'] = time();
		$data['site'] = $data['host'].'/'.$data['path'];

		$subject = Template::parse($tpl, $data, $mailroot.'-subject');
		$body = Template::parse($tpl, $data, $mailroot);

		return Mail::fromAdmin($subject, $email, $body);
	}
}
