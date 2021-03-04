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
    if (empty($user['email'])) return User::ret($ans, $lang, 'U021.a'.__LINE__);
    return User::ret($ans, $lang, 'U020.a'.__LINE__);

} else if ($action == 'create') {

    $email = Ans::REQ('email');
    if ($email) {
        if (!Mail::check($email)) return User::fail($ans, $lang, 'U006.a'.__LINE__);
        if ($user && $user['email'] == $email) return User::err($ans, $lang, 'U005.a'.__LINE__);
        $fuser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
        if ($fuser) return User::fail($ans, $lang, 'U008.a'.__LINE__);
    }

    $fuser = User::create($lang, $city_id, $timezone, $email); //user_id, token
    if (!$fuser) return User::fail($ans, $lang, 'U014.a'.__LINE__);
    $ans['user'] = $fuser;
    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

    return User::ret($ans, $lang, 'U017.a'.__LINE__);
} else if ($action == 'signin') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.a'.__LINE__);
    
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.a'.__LINE__);

    $password = Ans::REQ('password');
    if (!$password || $fuser['password'] !== $password) return User::err($ans, $lang, 'U018.a'.__LINE__);

    //При авторизации гостя сливаем
    if ($user) User::mergefromto($user, $fuser);

    $r = User::setEnv($fuser, $timezone, $lang, $city['city_id']);
    if (!$r) return User::fail($ans, $lang, 'Error.a'.__LINE__);
    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
    //depricated Авторизировать должен контроллер в REST-путях COOKIE.token. REST не работает с COOKKIE
    //signin возвращает token а контроллер-клиента его устанавливает
    //View::setCOOKIE('token', $ans['token']);

    return User::ret($ans, $lang, 'U020.a'.__LINE__);
} else if ($action == 'setenv') {
    $r = User::setEnv($user, $timezone, $lang, $city['city_id']);
    if (!$r) return User::fail($ans, $lang, 'Error.a'.__LINE__);
    return User::ret($ans);
// } else if ($action == 'settimezone') {
//     $r = User::setTimezone($fuser, $timezone);
//     if (!$r) return User::fail($ans, $lang, 'Error.a'.__LINE__);
//     return User::ret($ans);
// } else if ($action == 'setlang') {
//     $islang = Ans::REQ('lang', 'bool');
//     if (!$islang) return User::fail($ans, $lang, 'U037.a'.__LINE__);
//     $r = User::setLang($fuser, $lang);
//     if (!$r) return User::fail($ans, $lang, 'Error.a'.__LINE__);
//     return User::ret($ans);
// } else if ($action == 'setcity') {
//     $r = User::setCity($fuser, $city_id);
//     if (!$r) return User::fail($ans, $lang, 'Error.a'.__LINE__);
//     return User::ret($ans);
} else if ($action == 'signup') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.a'.__LINE__);
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);

    $olduser = User::getByEmail($email); // еще надо проверить есть ли уже такой емаил
    if ($olduser) return User::err($ans, $lang, 'U008.a'.__LINE__);

    $password = Ans::REQ('password');
    if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.a'.__LINE__);

    $repeatpassword = Ans::REQ('repeatpassword');
    if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.a'.__LINE__);

    $terms = Ans::REQ('terms');
    if (!$terms) return User::err($ans, $lang, 'U011.a'.__LINE__);

    //При авторизации гостя сливаем - user_id остаётся текущим, даже токен не меняем
    if ($user && empty($user['email'])) {
        User::setPassword($user, $password);
        User::setEmail($user, $email);
        //User::setToken($user);
        User::setEnv($user, $timezone, $lang, $city_id);
        $fuser = $user;
    } else {
        //if ($user['email']) Можно зарегистрироваться будучу уже зарегистрированным. Создасться новый пользователь и переключиться на него
        $fuser = User::create($lang, $city_id, $timezone, $email, $password); //user_id, token
        if (!$fuser) return User::fail($ans, $lang, 'U014.a'.__LINE__);
    }

    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];
    $ans['user'] = $fuser;

    $r = User::mail($fuser, $lang, 'welcome');
    if (!$r) return User::ret($ans, $lang, 'U012.a'.__LINE__);

    return User::ret($ans, $lang, 'U013.a'.__LINE__);
} else if ($action == 'logout') {
    if (!$user) return User::err($ans, $lang, 'U021.a'.__LINE__);
    $ans['token'] = '';
    return User::ret($ans, $lang, 'U022.a'.__LINE__);
} else if ($action == 'user') {
    $fuser = false;
    $email = Ans::REQ('email');
    if ($email) {
        if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);
        $fuser = User::getByEmail($email);
        if (!$fuser) return User::err($ans, $lang, 'U025.a'.__LINE__);
    }
    $user_id = Ans::REQ('user_id', 'int');
    if ($user_id) {
        $fuser = User::getById($user_id);
        if (!$fuser) return User::err($ans, $lang, 'U025.a'.__LINE__);
    }

    if (!$fuser) return User::err($ans, $lang, 'U024.a'.__LINE__);
    unset($fuser['password']);
    unset($fuser['token']);
    $ans['user'] = $fuser;
    return Ans::ret($ans);
} else if ($action == 'remind') {
    //Гость с корзиной может вспоминать пароль и при этом сохранить корзину. Но если ты в аккаунте с email тогда объединения не будет, при восстановлении прароля ты итак вернёшься к своей корзине
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.a'.__LINE__);

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);


    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.a'.__LINE__);

    if (isset($fuser['datemail']) && $fuser['datemail'] > time() - (60)) return User::err($ans, $lang, 'maildelay.a'.__LINE__);

    $r = User::mail($fuser, $lang, 'remind');
    if (!$r) return User::ret($ans, $lang, 'U012.a'.__LINE__);

    return User::ret($ans, $lang, 'U026.a'.__LINE__);
} else if ($action == 'getremindkey') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.a'.__LINE__);

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.a'.__LINE__);

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.a'.__LINE__);

    if (!$fuser['verify']) User::setVerify($fuser);
    return Ans::ret($ans);
} else if ($action == 'remindkey') {
    if (!empty($user['email'])) return User::err($ans, $lang, 'U005.a'.__LINE__);

    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::err($ans, $lang, 'U019.a'.__LINE__);

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.a'.__LINE__);

    $password = Ans::REQ('password');
    if (!User::checkPassword($password)) return User::err($ans, $lang, 'U009.a'.__LINE__);

    $repeatpassword = Ans::REQ('repeatpassword');
    if ($password != $repeatpassword) return User::err($ans, $lang, 'U010.a'.__LINE__);

    User::setPassword($fuser, $password);
    User::setToken($fuser); //При восстановлении пароля, token сбрасывается

    if ($user) User::mergefromto($user, $fuser); //Нужно данные из гостевого перенести в аккаунт где установлен счейчас новый пароль

    $ans['token'] = $fuser['user_id'] . '-' . $fuser['token'];

    $r = User::mail($fuser, $lang, 'newpass');

    //$ans['go'] = '/user';
    //$ans['popup'] = true;

    return User::ret($ans, $lang, 'U028.a'.__LINE__);
} else if ($action == 'change') {
    if (empty($user['email'])) return User::err($ans, $lang, 'U021.a'.__LINE__);

    $oldpassword = Ans::REQ('oldpassword');

    if ($user['password'] != $oldpassword) return User::err($ans, $lang, 'U029.a'.__LINE__);

    $newpassword = Ans::REQ('newpassword');
    if (!User::checkPassword($newpassword)) return User::err($ans, $lang, 'U009.a'.__LINE__);

    $repeatnewpassword = Ans::REQ('repeatnewpassword');
    if ($newpassword != $repeatnewpassword) return User::err($ans, $lang, 'U010.a'.__LINE__);

    User::setPassword($user, $newpassword);
    User::setToken($user);

    $ans['token'] = $user['user_id'] . '-' . $user['token'];

    User::mail($user, $lang, 'newpass');

    return User::ret($ans, $lang, 'U030.a'.__LINE__);
} else if ($action == 'confirm') {
    //if (!$submit) return User::fail($ans, $lang, 'U001.2');
    if (empty($user['email'])) return User::err($ans, $lang, 'U021.a'.__LINE__);
    if (!empty($user['verify'])) return User::ret($ans, $lang, 'U031.a'.__LINE__);
    $ans['datemail'] = $user['datemail'];

    if (isset($user['datemail']) && $user['datemail'] > time() - (60)) return User::err($ans, $lang, 'maildelay.a'.__LINE__);

    $r = User::mail($user, $lang, 'confirm');
    if (!$r) return User::err($ans, $lang, 'U012.a'.__LINE__);

    return User::ret($ans, $lang, 'U026.a'.__LINE__);
} else if ($action == 'list') {
    $ans['list'] = User::getList($lang);
    return Ans::ret($ans);
} else if ($action == 'confirmkey') {
    $email = Ans::REQ('email');
    if (!Mail::check($email)) return User::err($ans, $lang, 'U006.a'.__LINE__);

    $fuser = User::getByEmail($email);
    if (!$fuser) return User::fail($ans, $lang, 'U025.a'.__LINE__);

    $key = Ans::REQ('key');
    if (User::makeKey($fuser) != $key) return User::err($ans, $lang, 'U027.a'.__LINE__);

    if (!empty($fuser['verify'])) return User::ret($ans, $lang, 'U031.a'.__LINE__);

    User::setVerify($fuser);

    return User::ret($ans, $lang, 'U032.a'.__LINE__);
}
