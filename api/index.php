<?php

//use infrajs\nostore\Nostore;
//use infrajs\config\Config;
use infrajs\user\User;
//use infrajs\view\View;
use infrajs\load\Load;
use infrajs\ans\Ans;
use infrajs\rest\Rest;
use infrajs\mail\Mail;
use infrajs\path\Path;

header('Cache-Control: no-store');

$ans = [];

$lang = Ans::REQ('lang', User::$conf['lang']['list'], User::$conf['lang']['def']);

$token = Ans::REQ('token', 'string', '');
$user = User::fromToken($token);
$fuser = null;
$timezone = null;
$city_id = null;
$submit = ($_SERVER['REQUEST_METHOD'] === 'POST' || Ans::GET('submit', 'bool'));
$meta = Rest::meta();
if (!$meta) return User::fail($ans, $lang, 'U034.1I');

$action = Rest::first();

$root = Rest::getRoot();

$src = Path::theme($root . '/handlers.php');
$r = include($src);
if ($r !== 1) return $r;

$src = Path::theme($root . '/actions.php');
$r = include($src);
if ($r !== 1) return $r;
