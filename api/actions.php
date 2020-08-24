<?php

//use infrajs\nostore\Nostore;
//use infrajs\config\Config;
use infrajs\user\User;
//use infrajs\view\View;
use infrajs\load\Load;
use infrajs\ans\Ans;
use infrajs\rest\Rest;
use infrajs\mail\Mail;

if ($action == 'whoami') {
    unset($user['password']);
    unset($user['token']);
    unset($user['datecreate']);
    unset($user['datesignup']);
    unset($user['dateverify']);
    unset($user['datetoken']);
    unset($user['dateactive']);
    $ans['user'] = $user;
    if (empty($user['email'])) return User::ret($ans, $lang, 'U021.4A');
    return User::ret($ans, $lang, 'U020.2A');

} else if ($action == 'create') {

    $email = Ans::REQ('email');
    if ($email) {
        if (!Mail::check($email)) return User::fail($ans, $lang, 'U006.2A');
        if ($user && $user['email'] == $email) return User::err($ans, $lang, 'U005.2A');
        $fuser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
        if ($fuser) return User::fail($ans, $lang, 'U008.2A');
    }

    $fuser = User::create($lang, $city_id, $timezone, $email); //user_id, token
    if (!$fuser) return User::fail($ans, $lang, 'U014.2A');
    $ans['user'] = $fuser;
    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

    return User::ret($ans, $lang, 'U017.1A');
} else if ($action == 'signin') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.3A');
    
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.3A');

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.1A');

    $password = Ans::REQ('password');
    if (!$password || $fuser['password'] !== $password) return User::err($ans, $lang, 'U018.1A');

    //При авторизации гостя сливаем
    if ($user) User::mergefromto($user, $fuser);
    User::setTimezone($fuser, $timezone);
    User::setCity($fuser, $city_id);
    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
    //depricated Авторизировать должен контроллер в REST-путях COOKIE.token. REST не работает с COOKKIE
    //signin возвращает token а контроллер-клиента его устанавливает
    //View::setCOOKIE('token', $ans['token']);

    return User::ret($ans, $lang, 'U020.1A');
} else if ($action == 'settimezone') {
    $r = User::setTimezone($fuser, $timezone);
    if (!$r) return User::fail($ans, $lang, 'Error.1A');
    return User::ret($ans);
} else if ($action == 'setlang') {
    $islang = Ans::REQ('lang', 'bool');
    if (!$islang) return User::fail($ans, $lang, 'U037.1A');
    $r = User::setLang($fuser, $lang);
    if (!$r) return User::fail($ans, $lang, 'Error.2A');
    return User::ret($ans);
} else if ($action == 'setcity') {
    $r = User::setCity($fuser, $city_id);
    if (!$r) return User::fail($ans, $lang, 'Error.3A');
    return User::ret($ans);
} else if ($action == 'signup') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.1A');
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.1A');

    $olduser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
    if ($olduser) return User::err($ans, $lang, 'U008.1A');

    $password = Ans::REQ('password');
    if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.1A');

    $repeatpassword = Ans::REQ('repeatpassword');
    if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.1A');

    $terms = Ans::REQ('terms');
    if (!$terms) return User::err($ans, $lang, 'U011.1A');

    //При авторизации гостя сливаем
    if ($user && empty($user['email'])) {
        User::setPassword($user, $password);
        User::setEmail($user, $email);
        User::setToken($user);
        User::setTimezone($user, $timezone);
        User::setCity($user, $city_id);
        User::setLang($user, $lang);
        $fuser = $user;
    } else {
        $fuser = User::create($lang, $city_id, $timezone, $email, $password); //user_id, token
        if (!$fuser) return User::fail($ans, $lang, 'U014.1A');
    }

    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
    $ans['user'] = $fuser;

    $r = User::mail($fuser, $lang, 'welcome');
    if (!$r) return User::ret($ans, $lang, 'U012.1A');

    return User::ret($ans, $lang, 'U013.1A');
} else if ($action == 'logout') {
    if (!$user) return User::err($ans, $lang, 'U021.1A');
    $ans['token'] = '';
    return User::ret($ans, $lang, 'U022.1A');
} else if ($action == 'user') {
    $fuser = false;
    $email = Ans::REQ('email');
    if ($email) {
        if (!Mail::check($email)) return User::err($ans, $lang, 'U006.5A');
        $fuser = User::getByEmail($email);
        if (!$fuser) return User::err($ans, $lang, 'U025.1A');
    }
    $user_id = Ans::REQ('user_id', 'int');
    if ($user_id) {
        $fuser = User::getById($user_id);
        if (!$fuser) return User::err($ans, $lang, 'U025.2A');
    }

    if (!$fuser) return User::err($ans, $lang, 'U024.1A');
    unset($fuser['password']);
    unset($fuser['token']);
    $ans['user'] = $fuser;
    return Ans::ret($ans);
} else if ($action == 'remind') {
    //Гость с корзиной может вспоминать пароль и при этом сохранить корзину. Но если ты в аккаунте с email тогда объединения не будет, при восстановлении прароля ты итак вернёшься к своей корзине
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.4A');

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.4A');


    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.2A');

    if (isset($fuser['datemail']) && $fuser['datemail'] > time() - (60 * 5)) return User::err($ans, $lang, 'U033.1A');

    $r = User::mail($fuser, $lang, 'remind');
    if (!$r) return User::ret($ans, $lang, 'U012.2A');

    return User::ret($ans, $lang, 'U026.1A');
} else if ($action == 'getremindkey') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.5A');

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.5A');

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.3A');

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.1A');

    if (!$fuser['verify']) User::setVerify($fuser);
    return Ans::ret($ans);
} else if ($action == 'remindkey') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.5A');

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.5A');

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.3A');

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.1A');

    $password = Ans::REQ('password');
    if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.2A');

    $repeatpassword = Ans::REQ('repeatpassword');
    if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.2A');

    User::setPassword($fuser, $password);
    User::setToken($fuser); //При восстановлении пароля, token сбрасывается

    if ($user) User::mergefromto($user, $fuser); //Нужно данные из гостевого перенести в аккаунт где установлен счейчас новый пароль

    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

    $r = User::mail($fuser, $lang, 'newpass');

    //$ans['go'] = '/user';
    //$ans['popup'] = true;

    return User::ret($ans, $lang, 'U028.1A');
} else if ($action == 'change') {
    if (empty($user['email'])) return User::err($ans, $lang, 'U021.3A');

    $oldpassword = Ans::REQ('oldpassword');

    if ($user['password'] != $oldpassword) return User::err($ans, $lang, 'U029');

    $newpassword = Ans::REQ('newpassword');
    if (!User::checkPassword($newpassword)) return User::err($ans, $lang, 'U009.3A');

    $repeatnewpassword = Ans::REQ('repeatnewpassword');
    if ($newpassword != $repeatnewpassword) return User::err($ans, $lang, 'U010.1A');

    User::setPassword($user, $newpassword);
    User::setToken($user);

    $ans['token'] = $user['user_id'] . '-' . $user['token'];

    User::mail($user, $lang, 'newpass');

    return User::ret($ans, $lang, 'U030.1A');
} else if ($action == 'confirm') {
    //if (!$submit) return User::fail($ans, $lang, 'U001.2');
    if (empty($user['email'])) return User::err($ans, $lang, 'U021.2A');
    if (!empty($user['verify'])) return User::ret($ans, $lang, 'U031.1A');
    $ans['datemail'] = $user['datemail'];

    if (isset($user['datemail']) && $user['datemail'] > time() - (60 * 5)) return User::err($ans, $lang, 'U033.1A');

    $r = User::mail($user, $lang, 'confirm');
    if (!$r) return User::err($ans, $lang, 'U012.3A');

    return User::ret($ans, $lang, 'U026.2A');
} else if ($action == 'list') {
    $ans['list'] = User::getList();
    return Ans::ret($ans);
} else if ($action == 'confirmkey') {
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.6A');

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::fail($ans, $lang, 'U025.3A');

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.2A');

    if (!empty($fuser['verify'])) return User::ret($ans, $lang, 'U031.2A');

    User::setVerify($fuser);

    return User::ret($ans, $lang, 'U032.1A');
}
