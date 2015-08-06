<?php
namespace itlife\user;

infra_require('*session/session.php');
class User
{
	public static function is($group = false)
	{
		$email = infra_session_getEmail();
		if (!$email) {
			return false;
		}
		$verify = infra_session_getVerify();
		if (!$verify) {
			return false;
		}
		$conf = infra_config();
		infra_cache_no();
		if (!$group) {
			return true;
		}
		if (empty($conf['user'][$group])) {
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
		$json = '*user/get.php';
		$user = infra_loadJSON($json);

		return $user;
	}
	public static function sentEmail($email, $tpl, $data = array())
	{
		$conf=infra_config();
		call_user_func($conf['user']['sentEmail'], $email, $tpl, $data);
	}
	public static function getEmail()
	{
		return infra_session_getEmail();
	}
	public static function checkData($str, $type = 'value')
	{
		switch ($type) {
			case 'radio':
				return !!$str;
			case 'value':
				return $str && strlen($str) > 1;
			case 'password':
				return $str && strlen($str) > 5;
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
		$tpl = '*user/user.mail.tpl';

		$data['host'] = infra_view_getHost();
		$data['path'] = infra_view_getRoot();
		$data['email'] = $email;
		$data['time'] = time();
		$data['site'] = $data['host'].'/'.$data['path'];

		$subject = infra_template_parse($tpl, $data, $mailroot.'-subject');
		$body = infra_template_parse($tpl, $data, $mailroot);

		return infra_mail_fromAdmin($subject, $email, $body);
	}
}
