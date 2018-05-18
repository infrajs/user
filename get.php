<?php

use infrajs\user\User;
use infrajs\session\Session;
use infrajs\router\Router;
use infrajs\view\View;
use infrajs\ans\Ans;
use infrajs\access\Access;
use infrajs\nostore\Nostore;
use infrajs\config\Config;

$ans = array();
$submit = Ans::GET('submit','bool');
$type = Ans::GET('type','string');
$ans['id'] = Session::getId();

$ans['is'] = User::is();
$ans['admin'] = User::is('admin');

$myemail = Session::getEmail();
$ans['email'] = $myemail;
if ($type == 'signup') {
	if ($myemail) {
		return Ans::ret($ans, User::lang('You are logged in'));
	}
	if ($submit) {
		if (!$ans['id']) {
			return Ans::err($ans, User::lang('The error on the server. Session is not initialized, try again later.'));
		}

		$email = trim(strip_tags($_POST['email']));
		if (!User::checkData($email, 'email')) {
			return Ans::err($ans, User::lang('You need to provide a valid email'));
		}

		$user = Session::getUser($email);// еще надо проверить есть ли уже такой емаил
		if ($user['session_id']) {
			return Ans::err($ans, User::lang('This email already registered'));
		}

		$password = trim($_POST['password']);
		if (!User::checkData($password, 'password')) {
			return Ans::err($ans, User::lang('You must specify a valid password'));
		}

		$repeatpassword = trim($_POST['repeatpassword']);
		if ($password != $repeatpassword) {
			return Ans::err($ans, User::lang('Passwords do not match'));
		}

		$myemail = Session::getEmail();
		if ($myemail) {
			return Ans::err($ans, User::lang('You are already logged in'));
		}//Значит пользователь не зарегистрирован


		if (isset($_POST['terms'])) {
			$term = trim($_POST['terms']);
		} else {
			$term = false;
		}

		if (!$term) {
			return Ans::err($ans, User::lang('You need to accept the terms of service'));
		}

		//$password = md5($email.$password);
		$data = array();
		$data['key'] = md5($password.date('Y.m.j'));
		if (Access::debug()) $ans['data'] = $data;

		Session::setEmail($email);
		Session::setPass($password);
		View::setCookie(Session::getName('pass'), md5($password));
		
		Session::set('safe.confirmtime', time());
		$msg = User::sentEmail($email, 'signup', $data);
		if (is_string($msg)) return Ans::err($ans, $msg);
		$ans['go'] = '/user';
		
		return Ans::ret($ans, User::lang('You have successfully registered. We sent you a letter.'));
	}
}
if ($type == 'remindkey') {
	if ($myemail) {
		return Ans::err($ans, User::lang('You are already logged in'));
	}
	$key = $_REQUEST['key'];
	if (!$key) {
		return Ans::err($ans, User::lang('Incorrect link'));
	}
	$email = trim(strip_tags($_REQUEST['email']));
	if (!User::checkData($email, 'email')) {
		return Ans::err($ans, User::lang('Incorrect link'));
	}
	$userData = Session::getUser($email);
	$realkey = md5($userData['password'].date('Y.m.j'));
	if ($realkey !== $key) {
		return Ans::err($ans, User::lang('Link outdated'));
	}
	if ($submit) {
		$password = trim($_POST['password']);
		if (!User::checkData($password, 'password')) {
			return Ans::err($ans, User::lang('You must specify a valid password'));
		}
		$repeatpassword = trim($_POST['repeatpassword']);
		if ($password != $repeatpassword) {
			return Ans::err($ans, User::lang('Passwords do not match'));
		}

		//Session::setPass(md5($email.$password), $userData['session_id']);
		Session::setPass($password, $userData['session_id']);
		Session::change($userData['session_id']);

		$msg = User::sentEmail($email, 'newpass');
		$ans['go'] = '/user';
		$ans['popup'] = true;
		//Если popup true значит сообщение нужно дополнительно вывести во всплывающем окне'
		//Окно success требуется
		return Ans::ret($ans, User::lang('Password changed. You are logged in.'));
	}
}
if ($type == 'remind') {
	if ($myemail) {
		return Ans::err($ans, User::lang('You are logged in'));
	}
	$ans['time'] = Session::get('safe.remindtime');
	if ($submit) {
		$time = time();

		//При отладки слать можно подряд письма
	
		if ($ans['time'] && $ans['time'] + 5 * 60 > $time) {
			return Ans::err($ans, User::lang('Follow-up letter can be sent in 5 minutes'));
		}
		$email = trim(strip_tags($_POST['email']));

		if (!User::checkData($email, 'email')) {
			return Ans::err($ans, User::lang('You must specify a valid email address'));
		}
		if ($myemail) {
			return Ans::err($ans, User::lang('You are already logged in'));
		}

		$user = Session::getUser($email);
		if (!$user['session_id']) {
			return Ans::err($ans, User::lang('Email has not been registered yet'));
		}
		$data=array();
		$data['key'] = md5($user['password'].date('Y.m.j'));//Пароль для востановления действует только сегодня и после смены пароля действовать перестанет

		if (Access::debug()) {
			$ans['user'] = Session::getUser($email);
		}

		$msg = User::sentEmail($email, 'remind', $data);
		if (is_string($msg)) {
			return Ans::err($ans, $msg);
		}
		Session::set('safe.remindtime', time());

		return Ans::ret($ans, User::lang('We sent you a letter. Follow the instructions in the letter.'));
	}

	return Ans::ret($ans);
}
if ($type == 'confirm') {
	if (!$myemail) return Ans::err($ans, User::lang('You are not logged in'));
	$ans['time'] = Session::get('safe.confirmtime');
	
	$verify = Session::getVerify();
	if ($verify) {
		return Ans::ret($ans, User::lang('Address').' <b>'.$myemail.'</b> '.User::lang('already verified'));
	}
	
	if ($submit) {
		$oldtime = Session::get('safe.confirmtime');
		$time = time();
		if ($oldtime && $oldtime + 5 * 60 > $time) {
			return Ans::err($ans, User::lang('Follow-up letter can be sent in 5 minutes'));
		}
		

		$user = Session::getUser();
		if (!$user['email']) return Ans::err($ans, User::lang('Email has not been registered yet'));

		$data['key'] = md5($user['password'].date('Y.m.j'));

		Session::set('safe.confirmtime', $time);

		$msg = User::sentEmail($myemail, 'confirm', $data);
		if (is_string($msg)) {
			return Ans::err($ans, $msg);
		}

		return Ans::ret($ans, User::lang('We sent you a letter. Follow the instructions in the letter.'));
	}
}
if ($type == 'confirmkey') {
	
	
	$key = Ans::REQ('key');
	if (!$key) {
		return Ans::err($ans, User::lang('Incorrect link'));
	}
	$email = trim(strip_tags($_REQUEST['email']));
	if (!User::checkData($email, 'email')) {
		return Ans::err($ans, User::lang('Incorrect link'));
	}
	$user = Session::getUser($email);
	if (!$user) {
		return Ans::err($ans, User::lang('Link outdated'));
	}
	if (!empty($user['verify'])) {
		return Ans::ret($ans, User::lang('Address already verified'));
	}
	$realkey = md5($user['password'].date('Y.m.j'));
	if ($realkey !== $key) {
		return Ans::err($ans, User::lang('Link outdated'));
	}
	Session::setVerify($email);
	User::sentEmail($email, 'welcome');
	if (!$myemail) { //Сейчас нет авторизации
		Session::change($user['session_id']); //Вы авторизованы
	}
	return Ans::ret($ans, User::lang('All done. Address verified.'));
}
if ($type == 'change') {
	if (!$myemail) {
		return Ans::err($ans, User::lang('You are not logged in'));
	}
	if ($submit) {
		$oldpassword = trim($_POST['oldpassword']);
		$newpassword = trim($_POST['newpassword']);
		$repeatnewpassword = trim($_POST['repeatnewpassword']);

		if (!User::checkData($oldpassword, 'password')) {
			return Ans::err($ans, User::lang('You must specify a valid old password'));
		}

		//$oldpas = md5($myemail.$oldpassword);
		$oldpas = $oldpassword;
		$user = Session::getUser();
		if ($user['password'] != $oldpas) {
			return Ans::err($ans, User::lang('Invalid current password'));
		}

		if (!User::checkData($newpassword, 'password')) {
			return Ans::err($ans, User::lang('You must specify a valid new password'));
		}
		//$newpas = md5($myemail.$newpassword);
		$newpas = $newpassword;
		if ($newpassword != $repeatnewpassword) {
			return Ans::err($ans, User::lang('Passwords do not match'));
		}

		Session::setPass($newpas);
		View::setCookie(Session::getName('pass'), md5($newpas));
		$msg = User::sentEmail($myemail, 'newpass');

		return Ans::ret($ans, User::lang('Password changed'));
	}
}
if ($type == 'signin') {
	if ($myemail) {
		return Ans::err($ans, User::lang('You are already logged in'));
	}
	if ($submit) {
		$email = trim(strip_tags($_POST['email']));
		if (!User::checkData($email, 'email')) {
			return Ans::err($ans, User::lang('You must specify a valid email address'));
		}

		$userData = Session::getUser($email);
		$password = trim($_POST['password']);
		//if (md5($email.$password) != $userData['password']) {
		if ($password != $userData['password']) {
			return Ans::err($ans, User::lang('Wrong password or email'));
		}
		Session::change($userData['session_id']);

		$ans['go'] = '/user';

		return Ans::ret($ans, User::lang('You are logged in'));
	}
}
if ($type == 'logout') {
	if ($submit) {
		Session::logout();
		$ans['go'] = '/user';
		return Ans::ret($ans);
	}
	if (!$myemail) {
		return Ans::ret($ans, User::lang('You are not logged in'));
	}
}
if ($type == 'user') {
	$ans['verify'] = Session::getVerify();
}

return Ans::ret($ans);
