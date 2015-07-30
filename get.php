<?php

use itlife\user\User;

infra_require('*session/session.php');
$ans = array();
$submit = !empty($_GET['submit']);
$type = (string) @$_GET['type'];
$ans['id'] = infra_session_initId();

$ans['admin'] = User::isAdmin();

$myemail = infra_session_getEmail();
$ans['email'] = $myemail;
if ($type == 'signup') {
	if ($myemail) {
		return infra_err($ans, 'You are logged in.');
	}
	if ($submit) {
		if (!$ans['id']) {
			return infra_err($ans, 'The error on the server. Session is not initialized, try again later.');
		}

		$email = trim(strip_tags($_POST['email']));
		if (!User::checkData($email, 'email')) {
			return infra_err($ans, 'You must specify a valid email address');
		}

		$user = infra_session_getUser($email);// еще надо проверить есть ли уже такой емаил
		if ($user['session_id']) {
			return infra_err($ans, 'This email already registered.');
		}

		$password = trim($_POST['password']);
		if (!User::checkData($password, 'password')) {
			return infra_err($ans, 'You must specify a valid password');
		}

		$repeatpassword = trim($_POST['repeatpassword']);
		if ($password != $repeatpassword) {
			return infra_err($ans, 'Passwords do not match.');
		}

		$myemail = infra_session_getEmail();
		if ($myemail) {
			return infra_err($ans, 'You are already logged in');
		}//Значит пользователь не зарегистрирован


		$term = trim($_POST['terms']);
		if (!$term) {
			return infra_err($ans, 'You need to accept the terms of service.');
		}

		$password = md5($email.$password);
		$data = array();
		$data['key'] = md5($password.date('Y.m.j'));
		infra_session_setEmail($email);
		infra_session_setPass($password);
		infra_view_setCookie(infra_session_getName('pass'), md5($password));
		$ans['go'] = '?user';
		infra_session_set('safe.confirmtime', time());
		$msg = User::sentEmail($email, 'signup', $data);
		if (is_string($msg)) {
			return infra_err($ans, $msg);
		}

		return infra_ret($ans, 'You have successfully registered. We sent you a letter.');
	}
}
if ($type == 'remindkey') {
	if ($myemail) {
		return infra_err($ans, 'You are already logged in.');
	}
	$key = $_REQUEST['key'];
	if (!$key) {
		return infra_err($ans, 'Incorrect link');
	}
	$email = trim(strip_tags($_REQUEST['email']));
	if (!User::checkData($email, 'email')) {
		return infra_err($ans, 'Incorrect link');
	}
	$userData = infra_session_getUser($email);
	$realkey = md5($userData['password'].date('Y.m.j'));
	if ($realkey !== $key) {
		return infra_err($ans, 'Link outdated.');
	}
	if ($submit) {
		$password = trim($_POST['password']);
		if (!User::checkData($password, 'password')) {
			return infra_err($ans, 'You must specify a valid password');
		}
		$repeatpassword = trim($_POST['repeatpassword']);
		if ($password != $repeatpassword) {
			return infra_err($ans, 'Passwords do not match.');
		}

		infra_session_setPass(md5($email.$password), $userData['session_id']);
		infra_session_change($userData['session_id']);

		$msg = User::sentEmail($email, 'newpass');
		$ans['go'] = '?user';
		$ans['popup'] = true;
		//Если popup true значит сообщение нужно дополнительно вывести во всплывающем окне'
		//Окно success требуется
		return infra_ret($ans, 'Password changed. You are logged in.');
	}
}
if ($type == 'remind') {
	if ($myemail) {
		return infra_err($ans, 'You are logged in.');
	}
	$ans['time'] = infra_session_get('safe.remindtime');
	if ($submit) {
		$time = time();

		//При отладки слать можно подряд письма
		if (!infra_debug()) {
			if ($ans['time'] && $ans['time'] + 5 * 60 > $time) {
				return infra_err($ans, 'Follow-up letter can be sent in 5 minutes.');
			}
		}
		$email = trim(strip_tags($_POST['email']));

		if (!User::checkData($email, 'email')) {
			return infra_err($ans, 'You must specify a valid email address.');
		}
		if ($myemail) {
			return infra_err($ans, 'You are already logged in.');
		}

		$user = infra_session_getUser($email);
		if (!$user['session_id']) {
			return infra_err($ans, 'Email has not been registered yet.');
		}
		$data=array();
		$data['key'] = md5($user['password'].date('Y.m.j'));//Пароль для востановления действует только сегодня и после смены пароля действовать перестанет

		$msg = User::sentEmail($email, 'remind', $data);
		if (is_string($msg)) {
			return infra_err($ans, $msg);
		}
		infra_session_set('safe.remindtime', time());

		return infra_ret($ans, 'We sent you a letter. Follow the instructions in the letter.');
	}

	return infra_ret($ans);
}
if ($type == 'confirm') {
	if (!$myemail) {
		return infra_err($ans, 'You are not logged in.');
	}
	$ans['time'] = infra_session_get('safe.confirmtime');
	if ($submit) {
		$oldtime = infra_session_get('safe.confirmtime');
		$time = time();
		$conf = infra_config();
		if (!infra_debug()) {
			if ($oldtime && $oldtime + 5 * 60 > $time) {
				return infra_err($ans, 'Follow-up letter can be sent in 5 minutes.');
			}
		}

		$user = infra_session_getUser();
		if (!$user['email']) {
			return infra_err($ans, 'Email has not been registered yet.');
		}
		$data['key'] = md5($user['password'].date('Y.m.j'));

		infra_session_set('safe.confirmtime', $time);

		$msg = User::sentEmail($myemail, 'confirm', $data);
		if (is_string($msg)) {
			return infra_err($ans, $msg);
		}

		return infra_ret($ans, 'We sent you a letter. Follow the instructions in the letter.');
	}
}
if ($type == 'confirmkey') {
	if (!$myemail) {
		return infra_err($ans, 'You are not logged in.');
	}
	$verify = infra_session_getVerify();
	if ($verify) {
		return infra_ret($ans, 'Address already verified.');
	}
	$key = $_REQUEST['key'];
	if (!$key) {
		return infra_err($ans, 'Incorrect link');
	}
	$email = trim(strip_tags($_REQUEST['email']));
	if (!User::checkData($email, 'email')) {
		return infra_err($ans, 'Incorrect link');
	}

	$userData = infra_session_getUser();
	$realkey = md5($userData['password'].date('Y.m.j'));
	if ($realkey !== $key) {
		return infra_err($ans, 'Link outdated.');
	}
	infra_session_setVerify();
	User::sentEmail($myemail, 'welcome');
	return infra_ret($ans, 'All done. Address verified.');
}
if ($type == 'change') {
	if (!$myemail) {
		return infra_err($ans, 'You are not logged in.');
	}
	if ($submit) {
		$oldpassword = trim($_POST['oldpassword']);
		$newpassword = trim($_POST['newpassword']);
		$repeatnewpassword = trim($_POST['repeatnewpassword']);

		if (!User::checkData($oldpassword, 'password')) {
			return infra_err($ans, 'You must specify a valid old password.');
		}

		$oldpas = md5($myemail.$oldpassword);
		$user = infra_session_getUser();
		if ($user['password'] != $oldpas) {
			return infra_err($ans, 'Invalid current password.');
		}

		if (!User::checkData($newpassword, 'password')) {
			return infra_err($ans, 'You must specify a valid new password.');
		}
		$newpas = md5($myemail.$newpassword);
		if ($newpassword != $repeatnewpassword) {
			return infra_err($ans, 'Passwords do not match.');
		}

		infra_session_setPass($newpas);
		infra_view_setCookie(infra_session_getName('pass'), md5($newpas));
		$msg = User::sentEmail($myemail, 'newpass');

		return infra_ret($ans, 'Password changed.');
	}
}
if ($type == 'signin') {
	if ($myemail) {
		return infra_err($ans, 'You are already logged in.');
	}
	if ($submit) {
		$email = trim(strip_tags($_POST['email']));
		if (!User::checkData($email, 'email')) {
			return infra_err($ans, 'You must specify a valid email address.');
		}

		$userData = infra_session_getUser($email);
		$password = trim($_POST['password']);
		if (md5($email.$password) != $userData['password']) {
			return infra_err($ans, 'Wrong password or email.');
		}
		infra_session_change($userData['session_id']);
		$ans['go'] = '?user';

		return infra_ret($ans, 'You are logged in.');
	}
}
if ($type == 'logout') {
	if (!$myemail) {
		return infra_err($ans, 'You are not logged in.');
	}
	if ($submit) {
		infra_session_logout();
		$ans['go'] = '?user';

		return infra_ret($ans, 'Your status guest.');
	}
}
if ($type == 'user') {
	$ans['verify'] = infra_session_getVerify();
}

return infra_ret($ans);
