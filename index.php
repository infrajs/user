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
$back = Ans::GET('back','string');
if ($back) Session::set('user.back', $back);

$ans['id'] = Session::getId();

$ans['is'] = User::is();
$ans['admin'] = User::is('admin');

$myemail = Session::getEmail();
$ans['email'] = $myemail;

return Ans::ret($ans);
